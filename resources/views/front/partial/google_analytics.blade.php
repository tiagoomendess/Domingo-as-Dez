<!-- Google Analytics -->
<script>
    window.gtag_enable_tcf_support = true;
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}

    gtag('consent', 'default', {
        'ad_storage': 'granted',
        'ad_user_data': 'granted',
        'ad_personalization': 'granted',
        'analytics_storage': 'granted',
        'regions':['PT', 'ES', 'GB', 'FR', 'CH', 'NL']
    });

    gtag('consent', 'default', {
        'ad_storage': 'granted',
        'ad_user_data': 'granted',
        'ad_personalization': 'granted',
        'analytics_storage': 'granted'
    });
</script>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PPBRM1T8VZ"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-PPBRM1T8VZ');

    function consentGrantedAdStorage() {
        gtag('consent', 'update', {
            'ad_storage': 'granted'
        });
    }

    function consentGrantedAdUserData() {
        gtag('consent', 'update', {
            'ad_user_data': 'granted'
        });
    }

    function consentGrantedAdPersonalization() {
        gtag('consent', 'update', {
            'ad_personalization': 'granted'
        });
    }

    function consentGrantedAnalyticsStorage() {
        gtag('consent', 'update', {
            'analytics_storage': 'granted'
        });
    }

    // Listen for consent changes from AdSense consent platform
    window.addEventListener('load', function() {
        // Check if __tcfapi exists (IAB TCF framework used by Google Funding Choices)
        if (typeof __tcfapi !== 'undefined') {
            // Function to update consent based on TCF data
            var updateConsent = function(tcData, success) {
                console.log("Updating consent", success);
                if (success && tcData.gdprApplies !== undefined) {
                    // Update consent based on user choices
                    var analyticsConsent = tcData.purpose.consents[1] ? 'granted' : 'denied'; // Purpose 1 = Storage and access
                    var adsConsent = tcData.purpose.consents[3] ? 'granted' : 'denied'; // Purpose 3 = Personalized ads
                    
                    gtag('consent', 'update', {
                        'analytics_storage': analyticsConsent,
                        'ad_storage': adsConsent,
                        'ad_user_data': adsConsent,
                        'ad_personalization': adsConsent
                    });
                    
                    console.log('Consent updated - Analytics: ' + analyticsConsent + ', Ads: ' + adsConsent);
                }
            };
            
            // Check existing consent state (for returning users who already accepted)
            __tcfapi('getTCData', 2, updateConsent);
            
            // Listen for new consent changes (when user interacts with popup)
            __tcfapi('addEventListener', 2, function(tcData, success) {
                if (success && (tcData.eventStatus === 'useractioncomplete' || tcData.eventStatus === 'tcloaded')) {
                    updateConsent(tcData, success);
                }
            });
        }
        
        // Alternative: Listen for Google's consent API events
        if (typeof gtag !== 'undefined') {
            // Check consent state periodically (fallback method)
            var checkConsent = function() {
                gtag('get', 'G-PPBRM1T8VZ', 'client_id', function(clientId) {
                    if (clientId) {
                        console.log('Google Analytics tracking active');
                    } else {
                        console.log('Google Analytics tracking not active');
                    }
                });
            };
            setTimeout(checkConsent, 2500);
        }
    });
</script>
<!-- END Google Analytics -->
