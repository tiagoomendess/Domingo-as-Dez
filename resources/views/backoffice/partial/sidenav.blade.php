<ul id="slide-out" class="side-nav fixed">
    <li>
        <div class="user-view">
            <div class="background">
                <img src="https://picsum.photos/300/230?image=1058">
            </div>
            <a href="#"><img class="circle" src="{{ $user->profile->picture }}"></a>
            <a href="#!name"><span class="white-text name">{{ $user->name }}</span></a>
            <a href="#!email"><span class="white-text email">{{ $user->email }}</span></a>
        </div>
    </li>
    <li><a class="waves-effect" href="#">Artigos</a></li>
    <li><a class="waves-effect" href="#">Competições</a></li>
    <li><a class="waves-effect" href="#">Epocas</a></li>
</ul>