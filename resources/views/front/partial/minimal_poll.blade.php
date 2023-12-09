<div class="card">
    <div class="card-content">
        <h2 style="margin-bottom: 5px; font-size: 2rem; font-weight: 400; line-height: 2.2rem">{{ $poll->question }}</h2>
        <div class="divider"></div>
        <div class="vertical-spacer"></div>
        <div class="vertical-spacer"></div>

        @if($hasVoted)
            @if($showResultsAfter->timestamp < $now->timestamp)
                @include('front.partial.poll_votes')
            @else
                <p class="flow-text">Os resultados irão aparecer a partir
                    de {{ $showResultsAfter->format("d/m/Y à\s H:i") }}</p>
            @endif
        @else
            @if($now->timestamp > $closeAfter->timestamp)
                @include('front.partial.poll_votes')
            @else
                <div id="javascript_warning"><p class="flow-text center">A carregar</p></div>

                <form id="vote_form" action="{{ route('polls.front.vote', ['slug' => $poll->slug]) }}" method="POST"
                      class="hide">
                    @foreach($poll->answers as $index => $answer)
                        <p class="flow-text">
                            <input required name="answer" type="radio" id="{{ "reply_" . $index }}"
                                   value="{{ $answer->id }}"/>
                            <label for="{{ "reply_" . $index }}">{{ $answer->answer }}</label>
                        </p>
                        <div class="vertical-spacer"></div>
                    @endforeach

                    @if(empty(\Auth::user()))
                        <div class="row">
                            <div class="col xs12 s12">
                                {!! Recaptcha::render() !!}
                            </div>
                        </div>
                    @else
                        <div onclick="alerta()" class="vertical-spacer"></div>
                    @endif

                    <a id="submit_btn" class="green darken-3 btn waves-effect waves-light">Enviar
                        Resposta
                        <i class="material-icons right">send</i>
                    </a>

                    {{ csrf_field() }}
                    {{ method_field("PUT") }}
                </form>

                <div id="errors">
                    @if ($errors->count() > 0)
                        <blockquote>
                            <ul style="color: red;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error == "The ip field is required." ? "Algo correu mal, por favor tenta de novo" : $error }}</li>
                                @endforeach
                            </ul>
                        </blockquote>
                    @endif
                </div>

                <script>
                    setTimeout(() => {
                        $(document).ready(() => {
                            $('#javascript_warning').addClass('hide')
                            $('#vote_form').removeClass('hide')
                        });
                    }, 400)

                    document.getElementById('submit_btn').onclick = function (event) {
                        event.preventDefault();
                        $('#submit_btn').addClass('disabled').html("A Enviar <i class=\"material-icons right\">send</i>");

                        getIp("https://api.my-ip.io/v1/ip", async (ip) => {
                            if (ip) {
                                let ipInput = $(`<input type="hidden" name="ip" value="${ip}">`)
                                ipInput.appendTo('#vote_form')
                                $('#vote_form').submit()
                            } else {
                                let element = $(`<blockquote><ul style="color: red;"><li>Erro ao enviar informações. Por favor recarrega a página e tenta mais tarde.</li></ul></blockquote>`)
                                element.appendTo('#errors');
                                $('#submit_btn').removeClass('disabled').html("Enviar Resposta<i class=\"material-icons right\">send</i>");
                            }
                        })
                    };

                    async function getIp(theUrl, callback) {
                        let xmlHttp = new XMLHttpRequest();
                        xmlHttp.onreadystatechange = function () {
                            if (xmlHttp.readyState === 4) {
                                if (xmlHttp.status === 200)
                                    callback(xmlHttp.responseText)
                                else
                                    callback(null)
                            }
                        }
                        xmlHttp.open("GET", theUrl, true);
                        xmlHttp.send(null);
                    }

                </script>
            @endif
        @endif
    </div>
</div>

<script>

    setTimeout(() => {
        $(document).ready(() => {
            syncCookiesAndStorage()
        });
    }, 300)

    function syncCookiesAndStorage() {

        let cookies = getAllCookies();
        for (let i = 0; i < cookies.length; i++) {
            if (cookies[i].key.includes('poll')) {
                if (!getFromLocalStorage(cookies[i].key)) {
                    writeToLocalStorage(cookies[i].key, cookies[i].value)
                }
            }
        }

        let storageItems = getAllFromLocalStorage();
        for (let i = 0; i < storageItems.length; i++) {
            if (storageItems[i].key.includes('poll')) {
                if (!getCookie(storageItems[i].key)) {
                    setCookie(storageItems[i].key, storageItems[i].value)
                }
            }
        }
    }

    function getAllCookies()
    {
        let exploded = document.cookie.split(';')
        let allCookies = [];
        for (let i = 0; i < exploded.length; i++) {
            let split = exploded[i].split('=')
            if (split.length === 2) {
                allCookies.push({
                    key: split[0],
                    value: split[1]
                })
            }
        }

        return allCookies
    }

    function getAllFromLocalStorage()
    {
        let allItems = [];
        for (let i = 0, len = localStorage.length; i < len; ++i) {
            let key = localStorage.key(i)
            let value = localStorage.getItem(key)
            allItems.push({
                key: key,
                value: value
            })
        }

        return allItems;
    }

    function getFromLocalStorage(key)
    {
        let value = window.localStorage.getItem(key)

        return value ? { key: key, value: value } : null;
    }

    function writeToLocalStorage(key, value) {
        window.localStorage.setItem(key, value);
        return true;
    }

    function setCookie(key, value) {
        let date = new Date();
        date.setFullYear(date.getFullYear() + 1);

        let dateString = date.toLocaleDateString("en-US", {
            weekday: "short",
            year: "numeric",
            month: "short",
            day: "numeric",
        });

        let cookieString = `${key}=${value}; expires=${dateString}`
        document.cookie = cookieString;
        return true;
    }

    function getCookie(name) {
        let value = `; ${document.cookie}`;
        let parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
</script>
