jQuery(document).ready(function ($) {
    // Function to change the active content and adjust the active state of the tabs
    function setActiveTab(tabId) {
        var $tabs = $('#melicon-settings-tabs li');
        var $targetTab = $tabs.filter('[data-tab="' + tabId + '"]');
        var selectAction = 'melicon_settings_get_general_html';

        // Define the content based on the tab ID
        switch (tabId) {
            case "general":
                selectAction = 'melicon_settings_get_general_html';
                break;
            case "export":
                selectAction = 'melicon_settings_get_export_html';
                break;
            case "import":
                selectAction = 'melicon_settings_get_import_html';
                break;
            case "synchronizer":
                selectAction = 'melicon_settings_get_sync_html';
                break;
            default:
                console.error("Unknown Tab ID: ", tabId);
                break;
        }

        $('#setting-loader').show();

        // Perform the AJAX request
        $.ajax({
            type: "GET",
            url: ajaxurl,
            data: {
                "action": selectAction,
                "nonce": mcTranslations.ajax_settings_nonce
            },
            success: function (response) {
                // Insert the obtained HTML content into the tab container
                $('#tab-content').html(response);

                $tabs.removeClass('melicon-is-active');
                $targetTab.addClass('melicon-is-active');
            },
            error: function (xhr, status, error) {
                // Handle errors (optional)
                console.error("Error loading tab content: ", error);
                $('#tab-content').html('<p>Error loading content.</p>');
            },
            complete: function () {
                // Hide the loader once the request is complete
                $('#setting-loader').hide();
            }
        });
    }

    // Click handler for the tabs
    $('#melicon-settings-tabs li').click(function () {
        var tabId = $(this).data('tab');
        window.location.hash = tabId; // Update the hash in the URL
        setActiveTab(tabId);
    });


    // Check if there is a hash in the URL when the page loads and show the corresponding tab
    var currentTab = window.location.hash.replace('#', '') || 'general'; // 'general' as fallback
    if ($('#melicon-settings-tabs li[data-tab="' + currentTab + '"]').length) {
        setActiveTab(currentTab);
    } else {
        setActiveTab('general'); // Fallback to 'general' if the hash does not match any tab
    }
    
});