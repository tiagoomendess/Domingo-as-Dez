@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Log Viewer - {{ config('app.name') }}</title>
    
    <style>
        .log-viewer-container {
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
        }
        
        .log-files-nav {
            background: #fff;
            padding: 15px;
            border-bottom: 2px solid #e0e0e0;
            overflow-x: auto;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .log-file-tab {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: #424242;
        }
        
        .log-file-tab:hover {
            background: #eeeeee;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .log-file-tab.active {
            background: #2196F3;
            color: white;
            border-color: #1976D2;
            font-weight: 500;
        }
        
        .log-file-info {
            font-size: 11px;
            opacity: 0.8;
            display: block;
            margin-top: 3px;
        }
        
        .log-content-wrapper {
            flex: 1;
            overflow-y: auto;
            background: #1e1e1e;
            padding: 15px;
            font-family: 'Courier New', Consolas, monospace;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .log-line {
            margin: 2px 0;
            padding: 4px 8px;
            border-radius: 3px;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
        
        .log-line.emergency {
            background: #b71c1c;
            color: #fff;
            font-weight: bold;
        }
        
        .log-line.alert {
            background: #c62828;
            color: #fff;
            font-weight: bold;
        }
        
        .log-line.critical {
            background: #d32f2f;
            color: #fff;
            font-weight: bold;
        }
        
        .log-line.error {
            background: #c62828;
            color: #ffebee;
        }
        
        .log-line.warning {
            background: #f57c00;
            color: #fff3e0;
        }
        
        .log-line.notice {
            background: #1976d2;
            color: #e3f2fd;
        }
        
        .log-line.info {
            background: #0288d1;
            color: #e1f5fe;
        }
        
        .log-line.debug {
            background: #424242;
            color: #e0e0e0;
        }
        
        .log-line.default {
            color: #bdbdbd;
        }
        
        .log-header {
            background: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .log-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .auto-refresh-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-indicator.active {
            background: #4caf50;
            animation: pulse 2s infinite;
        }
        
        .status-indicator.inactive {
            background: #9e9e9e;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .log-stats {
            font-size: 13px;
            color: #757575;
        }
        
        .truncated-warning {
            background: #ff9800;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }
        
        .scroll-bottom-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #9e9e9e;
        }
        
        .empty-state i {
            font-size: 72px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s12">
            <h1>Log Viewer</h1>
        </div>
    </div>

    <div class="log-viewer-container">
        @if(count($files) > 0)
            <div class="log-files-nav">
                @foreach($files as $file)
                    <a href="{{ route('logs.index', ['file' => $file['name']]) }}" 
                       class="log-file-tab {{ $selectedFile === $file['name'] ? 'active' : '' }}"
                       data-file="{{ $file['name'] }}">
                        <i class="material-icons tiny" style="vertical-align: middle;">description</i>
                        {{ $file['name'] }}
                        <span class="log-file-info">{{ $file['size'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="log-header">
                <div class="log-stats">
                    <span id="log-file-name">
                        <i class="material-icons tiny" style="vertical-align: middle;">insert_drive_file</i>
                        {{ $selectedFile }}
                    </span>
                    @if($logContent && $logContent['truncated'])
                        <span class="chip orange lighten-4">
                            Showing last {{ $logContent['size'] }}
                        </span>
                    @endif
                    <span id="last-updated"></span>
                </div>
                
                <div class="log-controls">
                    <div class="auto-refresh-toggle">
                        <span class="status-indicator active" id="status-indicator"></span>
                        <span id="refresh-status">Auto-refresh: ON</span>
                    </div>
                    <button class="btn-small waves-effect waves-light blue" id="toggle-refresh">
                        <i class="material-icons left">pause</i>
                        Pause
                    </button>
                    <button class="btn-small waves-effect waves-light green" id="refresh-now">
                        <i class="material-icons left">refresh</i>
                        Refresh
                    </button>
                </div>
            </div>

            @if($logContent && $logContent['truncated'])
                <div class="truncated-warning">
                    <i class="material-icons" style="vertical-align: middle;">warning</i>
                    This log file is large. Showing only the most recent entries.
                </div>
            @endif

            <div class="log-content-wrapper" id="log-content">
                @if($logContent)
                    {!! $logContent['text'] ? parseLogContent($logContent['text']) : '<div class="empty-state">Log file is empty</div>' !!}
                @else
                    <div class="empty-state">
                        <i class="material-icons">error_outline</i>
                        <p>No log file selected or file not found</p>
                    </div>
                @endif
            </div>

            <a class="btn-floating btn-large waves-effect waves-light blue scroll-bottom-btn" id="scroll-bottom">
                <i class="material-icons">arrow_downward</i>
            </a>
        @else
            <div class="card">
                <div class="card-content">
                    <div class="empty-state">
                        <i class="material-icons">folder_open</i>
                        <p class="flow-text">No log files found</p>
                        <p>Log files will appear here when your application generates logs.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        let autoRefresh = true;
        let refreshInterval = null;
        const REFRESH_DELAY = 5000; // 5 seconds
        
        const selectedFile = '{{ $selectedFile }}';
        
        $(document).ready(function() {
            scrollToBottom();
            startAutoRefresh();
            
            $('#toggle-refresh').click(function() {
                autoRefresh = !autoRefresh;
                
                if (autoRefresh) {
                    $(this).html('<i class="material-icons left">pause</i>Pause');
                    $('#refresh-status').text('Auto-refresh: ON');
                    $('#status-indicator').removeClass('inactive').addClass('active');
                    startAutoRefresh();
                } else {
                    $(this).html('<i class="material-icons left">play_arrow</i>Resume');
                    $('#refresh-status').text('Auto-refresh: OFF');
                    $('#status-indicator').removeClass('active').addClass('inactive');
                    stopAutoRefresh();
                }
            });
            
            $('#refresh-now').click(function() {
                refreshLogs();
            });
            
            $('#scroll-bottom').click(function() {
                scrollToBottom();
            });
        });
        
        function startAutoRefresh() {
            if (!selectedFile) return;
            
            stopAutoRefresh();
            refreshInterval = setInterval(function() {
                if (autoRefresh) {
                    refreshLogs();
                }
            }, REFRESH_DELAY);
        }
        
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }
        
        function refreshLogs() {
            if (!selectedFile) return;
            
            $.ajax({
                url: '{{ route("logs.content") }}',
                method: 'GET',
                data: { file: selectedFile },
                success: function(response) {
                    $('#log-content').html(parseLogContent(response.content.text));
                    updateLastUpdated(response.timestamp);
                    
                    // Auto-scroll to bottom if user is already near bottom
                    const logContent = document.getElementById('log-content');
                    const isNearBottom = logContent.scrollHeight - logContent.scrollTop - logContent.clientHeight < 100;
                    
                    if (isNearBottom) {
                        scrollToBottom();
                    }
                },
                error: function(xhr) {
                    console.error('Error refreshing logs:', xhr);
                    M.toast({html: 'Error refreshing logs', displayLength: 2000});
                }
            });
        }
        
        function parseLogContent(content) {
            if (!content || content.trim() === '') {
                return '<div class="empty-state">Log file is empty</div>';
            }
            
            const lines = content.split('\n');
            let html = '';
            
            lines.forEach(function(line) {
                if (line.trim() === '') return;
                
                let level = 'default';
                
                if (line.match(/\.EMERGENCY:/i)) level = 'emergency';
                else if (line.match(/\.ALERT:/i)) level = 'alert';
                else if (line.match(/\.CRITICAL:/i)) level = 'critical';
                else if (line.match(/\.ERROR:/i)) level = 'error';
                else if (line.match(/\.WARNING:/i)) level = 'warning';
                else if (line.match(/\.NOTICE:/i)) level = 'notice';
                else if (line.match(/\.INFO:/i)) level = 'info';
                else if (line.match(/\.DEBUG:/i)) level = 'debug';
                
                const escapedLine = $('<div>').text(line).html();
                html += '<div class="log-line ' + level + '">' + escapedLine + '</div>';
            });
            
            return html;
        }
        
        function scrollToBottom() {
            const logContent = document.getElementById('log-content');
            if (logContent) {
                logContent.scrollTop = logContent.scrollHeight;
            }
        }
        
        function updateLastUpdated(timestamp) {
            $('#last-updated').html('<i class="material-icons tiny" style="vertical-align: middle;">access_time</i> Last updated: ' + timestamp);
        }
    </script>
@endsection

@php
function parseLogContent($content) {
    if (!$content || trim($content) === '') {
        return '<div class="empty-state">Log file is empty</div>';
    }
    
    $lines = explode("\n", $content);
    $html = '';
    
    foreach ($lines as $line) {
        if (trim($line) === '') continue;
        
        $level = 'default';
        
        if (preg_match('/\.EMERGENCY:/i', $line)) $level = 'emergency';
        elseif (preg_match('/\.ALERT:/i', $line)) $level = 'alert';
        elseif (preg_match('/\.CRITICAL:/i', $line)) $level = 'critical';
        elseif (preg_match('/\.ERROR:/i', $line)) $level = 'error';
        elseif (preg_match('/\.WARNING:/i', $line)) $level = 'warning';
        elseif (preg_match('/\.NOTICE:/i', $line)) $level = 'notice';
        elseif (preg_match('/\.INFO:/i', $line)) $level = 'info';
        elseif (preg_match('/\.DEBUG:/i', $line)) $level = 'debug';
        
        $escapedLine = htmlspecialchars($line);
        $html .= '<div class="log-line ' . $level . '">' . $escapedLine . '</div>';
    }
    
    return $html;
}
@endphp

