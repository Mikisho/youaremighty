<?php
$name = htmlspecialchars($_GET['name'] ?? 'Noname');
$defaultLang = strtolower(end(explode('.', $_SERVER['HTTP_HOST'])));
$lang = $_GET['lang'] ?? $defaultLang;
if (!file_exists(__DIR__ . '/texts/' . basename($lang) . '.json')) {
    $lang = 'en';
}
$translations = json_decode(file_get_contents(__DIR__ . '/texts/' . $lang . '.json'), true);
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#1e2327">
    <title><?= str_replace('{name}', $name, $translations['pageTitle']) ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<div class="container">
    <div class="content" id="content">
        <div class="slideshow">
            <div class="slide" id="slide0">
                <div>
                    <h1><?= str_replace('{name}', $name, $translations['header']) ?></h1>
                    <h3>Are you ready?</h3>
                    <button onclick="play()" id="playButton" style="visibility: hidden">Play 🔊</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var audio = new Audio('/audio.mp3');

    audio.addEventListener('canplaythrough', function () {
        document.getElementById('playButton').style.visibility = 'visible';
    }, false);

    var texts = <?=json_encode($translations['texts'])?>;
    texts[3] = '<div class="you"><?=$name?>></div>' + texts[3];
    texts[21] = '<div class="you"><?=$name?>></div>' + texts[21];

    var content = document.getElementById('content');
    var slideshow = document.createElement('div');
    slideshow.className = 'slideshow';

    var firstSlide = document.createElement('div');
    firstSlide.className = 'slide';
    firstSlide.id = 'slide0';
    firstSlide.innerHTML = '<div>' + document.getElementById('slide0').innerHTML + '</div>';
    slideshow.appendChild(firstSlide);

    for (var i = 0; i < texts.length; i++) {
        var slide = document.createElement('div');
        slide.classList.add('slide');
        slide.id = 'slide' + (i + 1);
        slide.innerHTML = '<div>' + texts[i] + '</div>';
        if (i >= 4) {
            var slideContent = slide.children[0];
            slideContent.style.transform = 'scale(6.0)';
            slideContent.classList.add('fast');
        }
        slideshow.appendChild(slide);
    }

    var currentVisibleSlide = 0;

    function play() {
        content.innerHTML = "";
        content.appendChild(slideshow);
        audio.play();
        setTimeout(hideSlide(0));
        setTimeout(nextSlide, 3200);
    }

    var times = [
        // 2nd part delay, current text visible, next slide gap
        [0, 0, 3200],
        [900, 2750, 1800],
        [900, 2850, 2300],
        [900, 2600, 1700],
        [2200, 4400, 600],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 950, 250],
        [0, 1900, -100],
        [0, 1900, -100],
        [0, 1900, -100],
        [0, 1900, 500],
        [1000, 99999999, 500],

    ];

    var startFromSlide = 0;

    if (startFromSlide) {
        var timesSums = times.map((t) => t[1] + t[2]);
        var offset = 0;
        for (var j = 0; j < startFromSlide; j++) {
            offset += times[j][1] + times[j][2];
        }
        offset -= times[startFromSlide - 1][2];
        audio.currentTime = offset / 1000;
        currentVisibleSlide = startFromSlide - 1;
    }

    function hideSlide(slideNumber) {
        return function () {
            var slide = slideshow.children[slideNumber].children[0];
            slide.style.opacity = '1';
            setTimeout(() => slide.style.opacity = '0');
            if (slideNumber === 4) {
                slide.style.transform = 'scale(8.0)';
                slide.getElementsByTagName('span')[0].style.opacity = '0';
                slide.getElementsByTagName('span')[0].style.transitionDuration = '0.1s !important';
            } else if (slideNumber > 4) {
                slide.style.transform = 'scale(0.5)';
            }
        }
    }

    function showSlide(slideNumber) {
        return function () {
            var slide = slideshow.children[slideNumber].children[0];
            // slide.style.display = 'block';
            slide.style.opacity = '0';
            setTimeout(() => slide.style.opacity = '1');
            setTimeout(() => slide.style.transform = 'scale(1.0)');
            var span = slide.getElementsByTagName('span')[0];
            if (span) {
                setTimeout(function () {
                    span.style.opacity = '1';
                }, times[slideNumber][0]);
            }
        }
    }

    function nextSlide() {
        ++currentVisibleSlide;
        var slideTimes = times[currentVisibleSlide];
        setTimeout(showSlide(currentVisibleSlide));
        setTimeout(hideSlide(currentVisibleSlide), slideTimes[1]);
        setTimeout(nextSlide, slideTimes[1] + slideTimes[2]);
        //
        // slideshow.children[currentVisibleSlide].style.opacity = '1';
        // slideshow.children[currentVisibleSlide].style.display = 'block';
        // setTimeout(() => slideshow.children[currentVisibleSlide].style.opacity = '0');
        // if (currentVisibleSlide === 4) {
        //     slideshow.children[currentVisibleSlide].style.transform = 'scale(8.0)';
        // } else if (currentVisibleSlide > 4) {
        //     slideshow.children[currentVisibleSlide].style.transform = 'scale(0.5)';
        // }
        // setTimeout(function () {
        //     slideshow.children[currentVisibleSlide].style.display = 'none';
        //     ++currentVisibleSlide;
        //     var currentSlide = slideshow.children[currentVisibleSlide];
        //     currentSlide.style.display = 'block';
        //     currentSlide.style.opacity = '0';
        //     if (currentVisibleSlide >= 5) {
        //         slideshow.children[currentVisibleSlide].style.transform = 'scale(6.0)';
        //         slideshow.children[currentVisibleSlide].classList.add('fast');
        //     }
        //     setTimeout(() => currentSlide.style.opacity = '1', 20);
        //     setTimeout(() => currentSlide.style.transform = 'scale(1.0)', 20);
        //     var span = currentSlide.getElementsByTagName('span')[0];
        //     if (span) {
        //         setTimeout(function () {
        //             span.style.opacity = '1';
        //         }, times[currentVisibleSlide][0]);
        //     }
        // }, times[currentVisibleSlide][2]);
        // setTimeout(nextSlide, times[currentVisibleSlide][2] + times[currentVisibleSlide + 1][1]);
    }

</script>
</body>
</html>
