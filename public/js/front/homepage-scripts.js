const news_time = 7000;
var select_news = 0;

function start() {
    startSlideShow();
}

function changeNewsTile() {
    var current_news = $('#news_snippets a.active');
    var next = current_news.next();

    if(next.length === 0)
        next = $('#news_snippets a').eq(0);

    current_news.attr('class', 'hide');
    next.attr('class', 'active');
}

function startSlideShow() {
    var delay = 25;
    var ticks = 0;
    var bar = $('.news-snippet-progress');

    setInterval(function() {

        var percent = (((ticks * delay) * 100) / news_time);
        bar.attr('style', 'width:' + percent + '%');

        ticks++;

        if (percent >= 98.5) {
            changeNewsTile();
            ticks = 0;
        }

    }, delay);

}

start();