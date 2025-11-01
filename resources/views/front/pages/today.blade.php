@extends('front.layouts.default-page')

@section('head-content')
    <title>
        @if($is_today)
            Jogos de Hoje
        @else
            Jogos de {{ $selected_date->format('d/m/Y') }}
        @endif
    </title>

    <meta property="og:title" content="{{ 'Jogos Hoje - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image" content="{{ url('/images/todays_games.jpg') }}">
    <meta property="og:image:width" content="1920">
    <meta property="og:image:height" content="1080">
    <meta property="og:description" content="Lista de todos os jogos marcados para o dia de hoje"/>

    <meta itemprop="name" content="Jogos de Hoje">
    <meta itemprop="description" content="Lista de todos os jogos marcados para o dia de hoje">
    <meta itemprop="image" content="{{ url('/images/todays_games.jpg') }}">
    
    <style>
        .date-nav-box {
            display: inline-flex;
            align-items: stretch;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px auto;
        }
        
        .date-nav-arrow {
            padding: 15px 20px;
            background-color: #f5f5f5;
            text-decoration: none;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: none;
            min-width: 50px;
        }
        
        .date-nav-arrow:hover {
            background-color: #1976d2;
            color: white;
        }
        
        .date-nav-arrow:active {
            background-color: #1565c0;
        }
        
        .date-display {
            padding: 15px 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: white;
            border-left: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            min-width: 200px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .date-display:hover {
            background-color: #f5f5f5;
        }
        
        /* Custom Date Picker Styles */
        .custom-datepicker-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        
        .custom-datepicker {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 20px;
            max-width: 350px;
            width: 90%;
        }
        
        .datepicker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .datepicker-header button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 1.2rem;
            color: #1976d2;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .datepicker-header button:hover {
            background-color: #e3f2fd;
        }
        
        .datepicker-month-year {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .datepicker-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-bottom: 5px;
        }
        
        .datepicker-weekday {
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            color: #666;
            padding: 8px 0;
        }
        
        .datepicker-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        .datepicker-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 50%;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .datepicker-day:hover {
            background-color: #e3f2fd;
        }
        
        .datepicker-day.other-month {
            color: #ccc;
        }
        
        .datepicker-day.selected {
            background-color: #1976d2;
            color: white;
            font-weight: 600;
        }
        
        .datepicker-day.today {
            border: 2px solid #1976d2;
            font-weight: 600;
        }
        
        @media only screen and (max-width: 600px) {
            .custom-datepicker {
                max-width: 320px;
            }
        }
        
        .date-display .day-name {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            text-transform: capitalize;
            line-height: 1.2;
        }
        
        .date-display .date-info {
            font-size: 1rem;
            color: #666;
            margin-top: 4px;
        }
        
        .date-display .today-badge {
            font-size: 1rem;
            color: #1976d2;
            font-weight: 500;
            margin-top: 4px;
        }
        
        .date-nav-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media only screen and (max-width: 600px) {
            .date-nav-box {
                margin: 15px auto;
            }
            
            .date-nav-arrow {
                padding: 12px 15px;
                min-width: 45px;
            }
            
            .date-display {
                padding: 12px 25px;
                min-width: 160px;
            }
            
            .date-display .day-name {
                font-size: 1.2rem;
            }
            
            .date-display .date-info,
            .date-display .today-badge {
                font-size: 0.9rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Date Navigation Bar -->
        <div class="date-nav-container">
            <div class="date-nav-box">
                <a href="{{ route('games.today') }}?date={{ $previous_date }}" class="date-nav-arrow">
                    <i class="material-icons">chevron_left</i>
                </a>
                
                <div class="date-display" id="date-display-trigger">
                    <div class="day-name">
                        {{ $selected_date->locale('pt')->isoFormat('dddd') }}
                    </div>
                    @if($is_today)
                        <div class="today-badge">Hoje</div>
                    @else
                        <div class="date-info">{{ $selected_date->format('d/m/Y') }}</div>
                    @endif
                </div>
                
                <a href="{{ route('games.today') }}?date={{ $next_date }}" class="date-nav-arrow">
                    <i class="material-icons">chevron_right</i>
                </a>
            </div>
        </div>

        <!-- Custom Date Picker -->
        <div class="custom-datepicker-overlay" id="datepicker-overlay">
            <div class="custom-datepicker" id="datepicker-widget">
                <div class="datepicker-header">
                    <button type="button" id="prev-month">&laquo;</button>
                    <div class="datepicker-month-year" id="month-year-display"></div>
                    <button type="button" id="next-month">&raquo;</button>
                </div>
                <div class="datepicker-weekdays">
                    <div class="datepicker-weekday">D</div>
                    <div class="datepicker-weekday">S</div>
                    <div class="datepicker-weekday">T</div>
                    <div class="datepicker-weekday">Q</div>
                    <div class="datepicker-weekday">Q</div>
                    <div class="datepicker-weekday">S</div>
                    <div class="datepicker-weekday">S</div>
                </div>
                <div class="datepicker-days" id="datepicker-days"></div>
            </div>
        </div>

        <h1 class="hide" style="margin-top: 0;">
            @if($is_today)
                Jogos de Hoje
            @else
                Jogos do Dia
            @endif
        </h1>

        @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
            <div class="hide-on-med-and-up" style="margin-top: 5px">
                <script async
                        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                        crossorigin="anonymous"></script>
                <!-- Today Games Horizontal -->
                <ins class="adsbygoogle"
                     style="display:block; width: 100%; max-height: 100px;"
                     data-ad-client="ca-pub-3518000096682897"
                     data-ad-slot="2210321320"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        @endif
        <!-- Loading Spinner (hidden by default) -->
        <div id="loading-spinner" class="row" style="display: none;">
            <div class="col s12 center-align" style="padding: 60px 0;">
                <div class="preloader-wrapper big active">
                    <div class="spinner-layer spinner-blue-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p style="margin-top: 20px; color: #666;">A carregar jogos...</p>
            </div>
        </div>

        <div class="row" id="games-content">
            <div class="col s12 m12 l8">
                @if (count($games) < 1)
                    <div class="card">
                        <div class="card-content group-games" style="padding: 10px">
                            <p class="flow-text text-center">
                                @if($is_today)
                                    Não existem jogos marcados para hoje.
                                @else
                                    Não existem jogos marcados para este dia.
                                @endif
                                @if($closest)
                                    O jogo mais próximo depois desta data está marcado para
                                    <a href="{{ route('games.today') }}?date={{ \Carbon\Carbon::parse($closest->date)->format('Y-m-d') }}">dia {{ (new \Carbon\Carbon($closest->date))->setTimezone('Europe/Lisbon')->format('d/m \d\e Y') }}</a>
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    @foreach($grouped_games as $group_data)
                        <div class="card" style="margin-bottom: 20px">
                            <div class="card-content" style="padding: 10px">
                                <div style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; background-color: #f5f5f5; border-radius: 4px">
                                    <img style="width: 35px; height: 35px; object-fit: contain; margin-right: 10px"
                                         src="{{ $group_data['game_group']->season->competition->picture }}"
                                         alt="{{ $group_data['game_group']->season->competition->name }}">
                                    <div>
                                        <h6 style="margin: 0; font-weight: bold">{{ $group_data['game_group']->name }}</h6>
                                        <span style="font-size: 0.9rem; color: #666">{{ $group_data['game_group']->season->competition->name }}</span>
                                    </div>
                                </div>

                                <ul class="list-a">
                                    @foreach($group_data['games'] as $game)
                                        <li>
                                            <a href="{{ $game->getPublicUrl() }}">

                                                <div class="row" style="margin-bottom: 0; width: 100%;">
                                                    <div class="col s5"
                                                         style="text-align: right; vertical-align: middle; vert-align: middle">
                                                        <div style="display: flex; flex-direction: row; justify-content: end; align-items: center; height: 37px">
                                                            <span style=""
                                                                  class="hide-on-med-and-down">{{ $game->home_team->club->name }}</span>
                                                            <span class="hide-on-large-only">
                                                                {{ mb_strtoupper(\Illuminate\Support\Str::limit($game->home_team->club->name, 3, '')) }}
                                                            </span>
                                                            <img class=""
                                                                 style="width: 30px; margin-left: 5px; resize: none;"
                                                                 src="{{ $game->home_team->club->getEmblem() }}">
                                                        </div>
                                                    </div>

                                                    <div class="col s2"
                                                         style="text-align: center; margin-top: 6px; padding: 0">
                                                            <span style="background-color: #989898; padding: 0.2rem 0.5rem; color: white; font-weight: bold">
                                                                @if ($game->finished)
                                                                    {{ $game->getHomeScore() }}
                                                                    - {{ $game->getAwayScore() }}
                                                                @else
                                                                    @if($game->postponed)
                                                                        ADI
                                                                    @else
                                                                        {{ (new \Carbon\Carbon($game->date))->setTimezone('Europe/Lisbon')->format('H:i') }}
                                                                    @endif
                                                                @endif
                                                            </span>
                                                    </div>

                                                    <div class="col s5">
                                                        <div style="display: flex; flex-direction: row; justify-content: start; align-items: center; height: 37px">
                                                            <img style="width: 30px; resize: none; margin-right: 5px"
                                                                 src="{{ $game->away_team->club->getEmblem() }}">
                                                            <span style=""
                                                                  class="hide-on-med-and-down">{{ $game->away_team->club->name }}</span>
                                                            <span class="hide-on-large-only">
                                                                {{ mb_strtoupper(\Illuminate\Support\Str::limit($game->away_team->club->name, 3, '')) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            @if(has_permission('score_update') && $is_today)
                <div class="row">
                    <div class="col s12">
                        <a href="{{ route('games.today_edit') }}"
                           class="btn-floating btn-large waves-effect waves-light blue right"><i class="material-icons">edit</i></a>
                    </div>
                </div>
            @endif

            @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
                <div class="row">
                    <div class="col col-xs-12 s12 m10 l8 offset-m1 offset-l2">
                        <script async
                                src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                                crossorigin="anonymous"></script>
                        <!-- Today Games Page -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-3518000096682897"
                             data-ad-slot="8596939730"
                             data-ad-format="auto"
                             data-full-width-responsive="true"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
            @endif

            @if(!$is_today)
                <div class="row">
                    <div class="col s12 center-align" style="margin-top: 5px;">
                        <a href="{{ route('games.today') }}" class="btn-flat waves-effect waves-light date-nav-trigger">
                            <i class="material-icons left">today</i>
                            Voltar ao dia de Hoje
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all date navigation elements (arrows + bottom button)
            const dateNavElements = document.querySelectorAll('.date-nav-arrow, .date-nav-trigger');
            
            dateNavElements.forEach(function(element) {
                element.addEventListener('click', function(e) {
                    // Prevent default navigation
                    e.preventDefault();
                    
                    // Show spinner
                    document.getElementById('loading-spinner').style.display = 'block';
                    
                    // Hide games content
                    document.getElementById('games-content').style.display = 'none';
                    
                    // Get the href and navigate programmatically
                    const href = this.getAttribute('href');
                    window.location.href = href;
                });
            });

            // Custom Date Picker Logic
            const overlay = document.getElementById('datepicker-overlay');
            const datepickerWidget = document.getElementById('datepicker-widget');
            const dateDisplayTrigger = document.getElementById('date-display-trigger');
            const monthYearDisplay = document.getElementById('month-year-display');
            const daysContainer = document.getElementById('datepicker-days');
            const prevMonthBtn = document.getElementById('prev-month');
            const nextMonthBtn = document.getElementById('next-month');
            
            let currentDate = new Date('{{ $selected_date->format('Y-m-d') }}');
            let displayDate = new Date(currentDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                               'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            
            function renderCalendar() {
                const year = displayDate.getFullYear();
                const month = displayDate.getMonth();
                
                monthYearDisplay.textContent = monthNames[month] + ' ' + year;
                
                // Get first day of month and number of days
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const prevLastDay = new Date(year, month, 0);
                
                const firstDayWeekday = firstDay.getDay();
                const daysInMonth = lastDay.getDate();
                const prevMonthDays = prevLastDay.getDate();
                
                daysContainer.innerHTML = '';
                
                // Previous month days
                for (let i = firstDayWeekday - 1; i >= 0; i--) {
                    const day = prevMonthDays - i;
                    const dayEl = document.createElement('div');
                    dayEl.className = 'datepicker-day other-month';
                    dayEl.textContent = day;
                    daysContainer.appendChild(dayEl);
                }
                
                // Current month days
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayDate = new Date(year, month, day);
                    const dayEl = document.createElement('div');
                    dayEl.className = 'datepicker-day';
                    dayEl.textContent = day;
                    
                    // Check if it's the selected date
                    if (dayDate.getTime() === currentDate.getTime()) {
                        dayEl.classList.add('selected');
                    }
                    
                    // Check if it's today
                    if (dayDate.getTime() === today.getTime()) {
                        dayEl.classList.add('today');
                    }
                    
                    dayEl.addEventListener('click', function() {
                        const selectedDate = new Date(year, month, day);
                        const formattedDate = selectedDate.getFullYear() + '-' + 
                                            String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' + 
                                            String(selectedDate.getDate()).padStart(2, '0');
                        
                        // Show spinner
                        document.getElementById('loading-spinner').style.display = 'block';
                        document.getElementById('games-content').style.display = 'none';
                        
                        // Close datepicker
                        overlay.style.display = 'none';
                        
                        // Navigate to the selected date
                        window.location.href = '{{ route('games.today') }}?date=' + formattedDate;
                    });
                    
                    daysContainer.appendChild(dayEl);
                }
                
                // Next month days to fill the grid
                const totalCells = daysContainer.children.length;
                const remainingCells = 42 - totalCells; // 6 rows * 7 days
                for (let day = 1; day <= remainingCells && remainingCells < 7; day++) {
                    const dayEl = document.createElement('div');
                    dayEl.className = 'datepicker-day other-month';
                    dayEl.textContent = day;
                    daysContainer.appendChild(dayEl);
                }
            }
            
            // Open datepicker
            dateDisplayTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                overlay.style.display = 'flex';
                renderCalendar();
            });
            
            // Close datepicker when clicking overlay
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.style.display = 'none';
                }
            });
            
            // Prevent closing when clicking inside the widget
            datepickerWidget.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            // Previous month
            prevMonthBtn.addEventListener('click', function() {
                displayDate.setMonth(displayDate.getMonth() - 1);
                renderCalendar();
            });
            
            // Next month
            nextMonthBtn.addEventListener('click', function() {
                displayDate.setMonth(displayDate.getMonth() + 1);
                renderCalendar();
            });
        });
    </script>
@endsection