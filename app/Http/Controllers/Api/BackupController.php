<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class BackupController extends Controller
{
    private string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
    }

    public function index(): JsonResponse
    {
        $backups = [];

        if (File::isDirectory($this->backupPath)) {
            $files = File::glob($this->backupPath . '/*.sql.gz');

            foreach ($files as $file) {
                $stat = File::lastModified($file);
                $backups[] = [
                    'filename' => basename($file),
                    'size' => File::size($file),
                    'size_human' => $this->formatBytes(File::size($file)),
                    'created_at' => date('Y-m-d H:i:s', $stat),
                ];
            }

            // Sort by date descending
            usort($backups, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        }

        return response()->json([
            'success' => true,
            'data' => $backups,
        ]);
    }

    public function store(): JsonResponse
    {
        // Only admin can backup
        if (auth()->user()?->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang bisa membuat backup.',
            ], 403);
        }

        File::ensureDirectoryExists($this->backupPath);

        $filename = date('Y-m-d_His') . '.sql.gz';
        $filepath = $this->backupPath . '/' . $filename;

        $database = config('database.connections.mysql.database');
        $extraFile = config('database.connections.mysql.backup_extra_file', '');

        // Build mysqldump command
        $cmd = 'mysqldump';

        if ($extraFile && File::exists($extraFile)) {
            $cmd .= " --defaults-extra-file=" . escapeshellarg($extraFile);
        } else {
            // Fallback: use env credentials
            $user = config('database.connections.mysql.username');
            $pass = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $cmd .= " -h " . escapeshellarg($host);
            $cmd .= " -u " . escapeshellarg($user);
            if ($pass) {
                $cmd .= " -p" . escapeshellarg($pass);
            }
        }

        $cmd .= " " . escapeshellarg($database) . " | gzip > " . escapeshellarg($filepath);

        $result = Process::run($cmd);

        if (!$result->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Backup gagal: ' . $result->errorOutput(),
            ], 500);
        }

        // Cleanup old backups (keep last 7)
        $this->cleanupOldBackups(7);

        return response()->json([
            'success' => true,
            'data' => [
                'filename' => $filename,
                'size' => File::size($filepath),
                'size_human' => $this->formatBytes(File::size($filepath)),
            ],
            'message' => 'Backup berhasil dibuat.',
        ]);
    }

    public function restore(Request $request): JsonResponse
    {
        // Only admin can restore
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang bisa restore backup.',
            ], 403);
        }

        $request->validate([
            'filename' => 'required|string',
        ]);

        $filename = $request->input('filename');
        $filepath = $this->backupPath . '/' . $filename;

        if (!File::exists($filepath)) {
            return response()->json([
                'success' => false,
                'message' => 'File backup tidak ditemukan.',
            ], 404);
        }

        $database = config('database.connections.mysql.database');
        $extraFile = config('database.connections.mysql.backup_extra_file', '');

        // Build restore command
        $cmd = 'gunzip -c ' . escapeshellarg($filepath) . ' | mysql';

        if ($extraFile && File::exists($extraFile)) {
            $cmd .= " --defaults-extra-file=" . escapeshellarg($extraFile);
        } else {
            $user = config('database.connections.mysql.username');
            $pass = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $cmd .= " -h " . escapeshellarg($host);
            $cmd .= " -u " . escapeshellarg($user);
            if ($pass) {
                $cmd .= " -p" . escapeshellarg($pass);
            }
        }

        $cmd .= " " . escapeshellarg($database);

        $result = Process::run($cmd);

        if (!$result->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Restore gagal: ' . $result->errorOutput(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Database berhasil di-restore dari backup.',
        ]);
    }

    public function download(string $filename): JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Only admin can download backups
        if (auth()->user()?->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang bisa mengunduh backup.',
            ], 403);
        }

        // Prevent path traversal — sanitize filename
        $filename = basename($filename);
        if (!str_ends_with($filename, '.sql.gz')) {
            return response()->json(['success' => false, 'message' => 'Nama file tidak valid.'], 400);
        }

        $filepath = $this->backupPath . '/' . $filename;

        if (!File::exists($filepath)) {
            return response()->json([
                'success' => false,
                'message' => 'File backup tidak ditemukan.',
            ], 404);
        }

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/gzip',
        ]);
    }

    public function destroy(string $filename): JsonResponse
    {
        // Only admin can delete backups
        if (auth()->user()?->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang bisa menghapus backup.',
            ], 403);
        }

        // Prevent path traversal — sanitize filename
        $filename = basename($filename);
        if (!str_ends_with($filename, '.sql.gz')) {
            return response()->json(['success' => false, 'message' => 'Nama file tidak valid.'], 400);
        }

        $filepath = $this->backupPath . '/' . $filename;

        if (!File::exists($filepath)) {
            return response()->json([
                'success' => false,
                'message' => 'File backup tidak ditemukan.',
            ], 404);
        }

        File::delete($filepath);

        return response()->json([
            'success' => true,
            'message' => 'Backup berhasil dihapus.',
        ]);
    }

    private function cleanupOldBackups(int $keep): void
    {
        $files = File::glob($this->backupPath . '/*.sql.gz');

        if (count($files) <= $keep) {
            return;
        }

        // Sort by modification time
        usort($files, fn($a, $b) => File::lastModified($b) - File::lastModified($a));

        // Delete old files
        foreach (array_slice($files, $keep) as $file) {
            File::delete($file);
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
