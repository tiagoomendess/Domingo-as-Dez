<style>
    .footer-ad-wrapper {
        max-height: 90px;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(255, 255, 255, 1);
        box-shadow: 0px -2px 10px rgba(0, 0, 0, 0.3); /* optional, for better visibility */
    }

    .footer-ad-outer-wrapper {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        max-height: 110px;
        z-index: 1000; /* high value to ensure it's above other content */
        overflow: auto; /* in case the content exceeds 100px in height */
    }

    .footer-ad-close {
        height: 20px;
        width: 100%;
        display: flex;
        justify-content: flex-end;
        align-items: flex-start;
        background-color: rgba(255, 255, 255, 0);
    }

    .footer-ad-close > div {
        height: 20px;
        width: 36px;
        color: #343434;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        right: 0;
        cursor: pointer;
    }
</style>

<div class="footer-ad-outer-wrapper hide-on-med-and-up">
    <div class="footer-ad-close">
        <div class="hide" onClick="handleCloseAd()"><i class="material-icons">expand_more</i></div>
    </div>

    <div class="footer-ad-wrapper">
        <script async
                src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                crossorigin="anonymous"></script>
        <!-- Footer Banner -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:728px;height:90px"
             data-ad-client="ca-pub-3518000096682897"
             data-ad-slot="1146112392"></ins>
    </div>
</div>

<script>
    const handleCloseAd = () => {
        document.querySelector('.footer-ad-outer-wrapper').classList.add('hide');
        document.querySelector('.footer-ad-spacer').style.display = 'none';
        document.querySelector('.footer-ad-wrapper').innerHTML = '';
        document.cookie = "ad_closed=true; max-age=300";
    }

    // wait for document load
    document.addEventListener('DOMContentLoaded', () => {
        // get window width
        const windowWidth = window.innerWidth;

        // Check if cookie exists and if it does, hide the ad
        if (document.cookie.indexOf('ad_closed=true') > -1) {
            document.querySelector('.footer-ad-outer-wrapper').style.display = 'none';
            document.querySelector('.footer-ad-wrapper').innerHTML = '';
            document.querySelector('.footer-ad-spacer').classList.add('hide');
        } else {
            // change ins width to window width
            document.querySelector('.footer-ad-wrapper > ins').style.width = windowWidth + 'px';

            (adsbygoogle = window.adsbygoogle || []).push({});
            // Only show close button after 7 seconds
            const timoutId = setTimeout(() => {
                document.querySelector('.footer-ad-close > div').classList.remove('hide');
                clearTimeout(timoutId);
            }, 7000);
        }
    });
</script>
