<?php

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';

$siteName = (string) ($config['site_name'] ?? 'Online Consultant');
$pluginId = (string) ($config['plugin_id'] ?? 'g1l7xa9');
$jsSrc    = (string) ($config['widget_js_src'] ?? '');
if ($jsSrc === '') {
    $jsSrc = $baseUrl . '/widget.js';
}
$bgImage = (string) ($config['public_bg_image'] ?? '');
$bgAttr  = ($bgImage !== '')
    ? "\n            background-image: url('" . e($bgImage) . "');"
    : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="google" content="notranslate" />
    <title><?= e($siteName) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-width: 100vw;
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center;
            background-color: #eee;<?= $bgAttr ?>
        }
    </style>
</head>

<body>

    <div class="container"></div>
    <script id="insertJs"></script>

    <script>
        let plugin_id = "<?= e($pluginId) ?>"
        let js_src = "<?= e($jsSrc) ?>"
        let is_chinese_ip =  0 

        const container = document.querySelector('.container')

        const standby_text = document.createElement('p')
        standby_text.innerText = 'The link is invalid, Please check whether the plugin is opened normally'

        const not_allowed = document.createElement('p')
        not_allowed.innerText = 'Chinese IP is not allowed'

        function handlePluginHide() {
            const widget = document.querySelector('salesmartly-chat-widget');
            if (widget) widget.remove();

            if(is_chinese_ip){
                container.appendChild(not_allowed);
                return true
            }
            container.appendChild(standby_text);
            return true
        }

        function handleShowMode(type) {
            if (type === 'exclusiveLinkNoOpen') {
                handlePluginHide()
            }
        }

        if (plugin_id) {
            const $js = document.getElementById('insertJs')
            if ($js) $js.innerHTML = `
                (function(d, s, id, w, n) {
                    w.__ssc = w.__ssc || {};
                    w.__ssc.license = ${JSON.stringify(plugin_id)};
                    if (w.ssq) return false;
                    n = w.ssq = function() {n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                    n.push=n;n.loaded=!0;n.queue=[];
                    var isWidgetLoaded = false;
                    function loadWidget() {
                        if (isWidgetLoaded || d.getElementById(id)) return;
                        isWidgetLoaded = true;
                        var js, sjs = d.getElementsByTagName(s)[0];
                        js = d.createElement(s); js.id = id;
                        var deUrl = atob('aHR0cHM6Ly9wbHVnaW4tY29kZS5zYWxlc21hcnRseS5jb20='), path = '/chat/widget-v2/testing/install.js';
                        var cs = d.currentScript, csUrl = deUrl;
                        if (cs && cs.src) {var scriptURL = new URL(cs.src); csUrl = scriptURL.origin;}
                        js.src = ${JSON.stringify(js_src)};
                        sjs.parentNode.insertBefore(js, sjs);
                        js.onerror = function() {
                            if (d.getElementById(id)) {var el = d.getElementById(id); el.parentNode.removeChild(el);}
                            var newJs = d.createElement(s); newJs.id = id;
                            newJs.src = deUrl + path;
                            sjs.parentNode.insertBefore(newJs, sjs);
                        }
                    }
                    var searchParams = new URLSearchParams(w.location.search);
                    var loginInfoText = searchParams.get('setLoginInfo');
                    var loginInfo;
                    if (loginInfoText) {
                        try {
                            var parsedLoginInfo = JSON.parse(loginInfoText);
                            if (parsedLoginInfo && typeof parsedLoginInfo === 'object' && !Array.isArray(parsedLoginInfo)) {
                                loginInfo = parsedLoginInfo;
                            }
                        } catch (error) {}
                    }
                    if (loginInfo) {
                        w.ssq.push('setLoginInfo', loginInfo);
                        loadWidget();
                        return;
                    }
                    if (searchParams.get('loginSource') !== 'postMessage') {
                        loadWidget();
                        return;
                    }
                    function handleLoginMessage(event) {
                        if (event.source !== w || event.origin !== w.location.origin || typeof event.data !== 'string') return;
                        var message;
                        try {
                            message = JSON.parse(event.data);
                        } catch (error) {
                            return;
                        }
                        if (!message || typeof message !== 'object' || Array.isArray(message)) return;
                        if (message.type !== 'service-link-login' || message.version !== 1) return;
                        var payload = message.payload;
                        if (!payload || typeof payload !== 'object' || Array.isArray(payload)) return;
                        w.removeEventListener('message', handleLoginMessage);
                        w.ssq.push('setLoginInfo', payload);
                    }
                    w.addEventListener('message', handleLoginMessage);
                    loadWidget();
                }(document, 'script', 'ss-chat', window));
                
                window.__ssc.setting = {mode:'exclusiveLink', modeSelector:  '#ss-chat-page', overTime: '',  isCustomized: 0};
            `

            window.ssq.push('setExclusiveLink', (data) => {
                const { type = ''} = data
                handleShowMode(type)
            })
        } else {
            handlePluginHide()
        }
    </script>
</body>

</html>
