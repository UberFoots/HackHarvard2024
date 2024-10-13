<?php
function getDeviceType() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($userAgent, 'iPhone') || strpos($userAgent, 'iPad') || strpos($userAgent, 'iPod')) {
        return 'ios';
    } elseif (strpos($userAgent, 'Android')) {
        return 'android';
    } else {
        return 'desktop';
    }
}

$deviceType = getDeviceType();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylete - Sustainable Fashion at Your Fingertips</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .circle-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .step-text {
            display: flex;
            align-items: center;
            text-align: left;
            min-height: 40px;
        }
    </style>
</head>
<body class="min-h-screen bg-white text-gray-900 font-sans">
    <header class="flex justify-between items-center p-4 md:p-6">
        <div class="flex items-center">
            <img src="/assets/img/icon.png" alt="Stylete Logo" class="w-20 h-20 mr-2 rounded-2xl">
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 md:py-16 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">
            Sustainable Fashion<br>at Your Fingertips
        </h1>
        <p class="text-xl md:text-2xl mb-8">
            Discover and shop eco-friendly fashion.
        </p>
        
        <!-- Installation Instructions -->
        <div class="mb-8">
            <div id="installInstructions" class="flex flex-col md:flex-row justify-center gap-6 px-4">
                <?php if ($deviceType === 'ios'): ?>
                <div class="bg-purple-600 text-white p-4 rounded-lg flex-1 max-w-sm">
                    <h3 class="text-xl font-bold mb-4">iOS Installation</h3>
                    <ol class="text-left">
                        <li class="flex items-start mb-4">
                            <div class="circle-icon bg-white text-purple-600 mr-4">1</div>
                            <span class="step-text flex-grow font-bold">
                                Navigate to the site in Safari
                            </span>
                        </li>
                        <li class="flex items-start mb-4">
                            <div class="circle-icon bg-white text-purple-600 mr-4">2</div>
                            <span class="step-text flex-grow font-bold">
                                Press the Share button
                            </span>
                        </li>
                        <li class="flex items-start">
                            <div class="circle-icon bg-white text-purple-600 mr-4">3</div>
                            <span class="step-text flex-grow font-bold">
                                Select "Add to Home Screen" from the pop-up
                            </span>
                        </li>
                    </ol>
                </div>
                <?php elseif ($deviceType === 'android'): ?>
                <div class="bg-purple-600 text-white p-4 rounded-lg flex-1 max-w-sm">
                    <h3 class="text-xl font-bold mb-4">Android Installation</h3>
                    <ol class="text-left">
                        <li class="flex items-start mb-4">
                            <div class="circle-icon bg-white text-purple-600 mr-4">1</div>
                            <span class="step-text flex-grow font-bold">
                                Open the site in Chrome
                            </span>
                        </li>
                        <li class="flex items-start mb-4">
                            <div class="circle-icon bg-white text-purple-600 mr-4">2</div>
                            <span class="step-text flex-grow font-bold">
                                Tap the menu icon (⋮)
                            </span>
                        </li>
                        <li class="flex items-start">
                            <div class="circle-icon bg-white text-purple-600 mr-4">3</div>
                            <span class="step-text flex-grow font-bold">
                                Tap "Add to Home screen"
                            </span>
                        </li>
                    </ol>
                </div>
                <?php else: ?>
                <div class="bg-gray-100 p-4 rounded-lg flex-1 max-w-sm">
                    <h3 class="text-xl font-bold mb-4">Desktop</h3>
                    <p class="text-left">
                        To install the app, please navigate to this site on a mobile device. Alternatively, you can use the web version on your desktop browser.
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <p class="mb-8">
            Want to onboard as a merchant?
            <a href="/merchants/register" class="text-purple-500 hover:underline">Register Now.</a>
        </p>
        <p class="mb-8">Available on <a href="/home" class="text-purple-500 hover:underline">Web</a>, iOS and Android.</p>
        <div class="relative w-full max-w-3xl mx-auto aspect-video bg-black rounded-lg overflow-hidden">
            <iframe 
                src="https://www.youtube.com/embed/dQw4w9WgXcQ" 
                width="640" 
                height="480" 
                alt="App Demo Video Thumbnail" 
                class="w-full h-full object-cover" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function getMobileOperatingSystem() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;

            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                return "ios";
            }

            if (/android/i.test(userAgent)) {
                return "android";
            }

            return "desktop";
        }

        var deviceType = getMobileOperatingSystem();
        var instructionsDiv = document.getElementById('installInstructions');

        if (deviceType !== '<?php echo $deviceType; ?>') {
            // Client-side detection differs from server-side, update the instructions
            if (deviceType === 'ios') {
                instructionsDiv.innerHTML = `
                    <div class="bg-purple-600 text-white p-4 rounded-lg flex-1 max-w-sm">
                        <h3 class="text-xl font-bold mb-4">iOS Installation</h3>
                        <ol class="text-left">
                            <li class="flex items-start mb-4">
                                <div class="circle-icon bg-white text-purple-600 mr-4">1</div>
                                <span class="step-text flex-grow font-bold">
                                    Navigate to the site in Safari
                                </span>
                            </li>
                            <li class="flex items-start mb-4">
                                <div class="circle-icon bg-white text-purple-600 mr-4">2</div>
                                <span class="step-text flex-grow font-bold">
                                    Press the Share button
                                </span>
                            </li>
                            <li class="flex items-start">
                                <div class="circle-icon bg-white text-purple-600 mr-4">3</div>
                                <span class="step-text flex-grow font-bold">
                                    Select "Add to Home Screen" from the pop-up
                                </span>
                            </li>
                        </ol>
                    </div>
                `;
            } else if (deviceType === 'android') {
                instructionsDiv.innerHTML = `
                    <div class="bg-purple-600 text-white p-4 rounded-lg flex-1 max-w-sm">
                        <h3 class="text-xl font-bold mb-4">Android Installation</h3>
                        <ol class="text-left">
                            <li class="flex items-start mb-4">
                                <div class="circle-icon bg-white text-purple-600 mr-4">1</div>
                                <span class="step-text flex-grow font-bold">
                                    Open the site in Chrome
                                </span>
                            </li>
                            <li class="flex items-start mb-4">
                                <div class="circle-icon bg-white text-purple-600 mr-4">2</div>
                                <span class="step-text flex-grow font-bold">
                                    Tap the menu icon (⋮)
                                </span>
                            </li>
                            <li class="flex items-start">
                                <div class="circle-icon bg-white text-purple-600 mr-4">3</div>
                                <span class="step-text flex-grow font-bold">
                                    Tap "Add to Home screen"
                                </span>
                            </li>
                        </ol>
                    </div>
                `;
            } else {
                instructionsDiv.innerHTML = `
                    <div class="bg-gray-100 p-4 rounded-lg flex-1 max-w-sm">
                        <h3 class="text-xl font-bold mb-4">Desktop</h3>
                        <p class="text-left">
                            To install the app, please navigate to this site on a mobile device. Alternatively, you can use the web version on your desktop browser.
                        </p>
                    </div>
                `;
            }
        }
    });
    </script>
</body>
</html>