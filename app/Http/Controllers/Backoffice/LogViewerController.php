<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogViewerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin');
    }

    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        $files = $this->getLogFiles($logPath);
        
        $selectedFile = $request->query('file', count($files) > 0 ? $files[0]['name'] : null);
        $logContent = null;
        
        if ($selectedFile && File::exists($logPath . '/' . $selectedFile)) {
            $logContent = $this->readLogFile($logPath . '/' . $selectedFile);
        }
        
        return view('backoffice.pages.log_viewer', [
            'files' => $files,
            'selectedFile' => $selectedFile,
            'logContent' => $logContent
        ]);
    }

    public function getContent(Request $request)
    {
        $fileName = $request->query('file');
        
        if (!$fileName) {
            return response()->json(['error' => 'No file specified'], 400);
        }
        
        $logPath = storage_path('logs/' . $fileName);
        
        if (!File::exists($logPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        $content = $this->readLogFile($logPath);
        
        return response()->json([
            'content' => $content,
            'file' => $fileName,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    private function getLogFiles($path)
    {
        if (!File::exists($path)) {
            return [];
        }
        
        $files = File::files($path);
        $logFiles = [];
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'log') {
                $logFiles[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $this->formatBytes($file->getSize()),
                    'modified' => $file->getMTime(),
                ];
            }
        }
        
        // Sort by modification time, most recent first
        usort($logFiles, function ($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return $logFiles;
    }

    private function readLogFile($path)
    {
        if (!File::exists($path)) {
            return null;
        }
        
        $fileSize = File::size($path);
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if ($fileSize > $maxSize) {
            // Read only the last 2MB of the file
            $handle = fopen($path, 'r');
            fseek($handle, -$maxSize, SEEK_END);
            $content = fread($handle, $maxSize);
            fclose($handle);
            
            // Remove incomplete first line
            $content = substr($content, strpos($content, "\n") + 1);
            $truncated = true;
        } else {
            $content = File::get($path);
            $truncated = false;
        }
        
        return [
            'text' => $content,
            'truncated' => $truncated,
            'size' => $this->formatBytes($fileSize)
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

