function addListener(a) {
    navigator.s3detectBrowser = function() {
        var a, n = navigator.userAgent,
            t = n.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        return /trident/i.test(t[1]) ? (a = /\brv[ :]+(\d+)/g.exec(n) || [], "IE " + (a[1] || "")) : "Chrome" === t[1] && (a = n.match(/\bOPR\/(\d+)/), null != a) ? "Opera " + a[1] : (t = t[2] ? [t[1], t[2]] : [navigator.appName, navigator.appVersion, "-?"], null != (a = n.match(/version\/(\d+)/i)) && t.splice(1, 1, a[1]), t.join(" "))
    }();
    var n = {
            Android: function() {
                return navigator.userAgent.match(/Android/i)
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i)
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i)
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i)
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i)
            },
            any: function() {
                return n.Android() || n.BlackBerry() || n.iOS() || n.Opera() || n.Windows()
            }
        },
        t = {
            app_id: a.app_id,
            user_ip: a.server,
            bucket: a.bucket,
            browser: navigator.s3detectBrowser,
            navigator_user_agent: navigator.userAgent,
            navigator_vendor: navigator.vendor,
            navigator_product: navigator.product,
            navigator_hardware: navigator.hardwareConcurrency,
            navigator_cookie: navigator.cookieEnabled,
            navigator_language: navigator.language,
            navigator_languages: JSON.stringify(navigator.languages),
            location_host: location.host,
            location_hostname: location.hostname,
            location_href: location.href,
            location_origin: location.origin,
            location_pathname: location.pathname,
            location_protocol: location.protocol,
            mobile: n.any() ? !0 : !1,
            advert: a.advert,
            key: a.key,
            type: a.type
        };
    jQuery.post("https://api.s3bubble.com/v1/analytics/add", t, function() {}, "json")
}