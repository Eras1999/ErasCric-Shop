(function (window, document, $) {
    "use strict";
    var BdThemesLiveCopy = {
        //Initializing properties and methods
        init: function (e) {
            BdThemesLiveCopy.loadContextMenuGroupsHooks();
        },
        loadContextMenuGroupsHooks: function () {
            elementor.hooks.addFilter(
                "elements/container/contextMenuGroups",
                function (groups, element) {
                    return BdThemesLiveCopy.prepareMenuItem(groups, element);
                }
            );

            elementor.hooks.addFilter(
                "elements/section/contextMenuGroups",
                function (groups, element) {
                    return BdThemesLiveCopy.prepareMenuItem(groups, element);
                }
            );

            elementor.hooks.addFilter(
                "elements/widget/contextMenuGroups",
                function (groups, element) {
                    return BdThemesLiveCopy.prepareMenuItem(groups, element);
                }
            );

            elementor.hooks.addFilter(
                "elements/column/contextMenuGroups",
                function (groups, element) {
                    return BdThemesLiveCopy.prepareMenuItem(groups, element);
                }
            );
        },
        prepareMenuItem: function (groups, element) {
            var index = _.findIndex(groups, function (element) {
                return "clipboard" === element.name;
            });
            groups.splice(index + 1, 0, {
                name: "bdt-ep-live-copy-paste",
                actions: [
                    {
                        name: "ep-live-copy",
                        title: "Live Copy",
                        icon: "bdt-wi-element-pack",
                        callback: function () {
                            alert(
                                "Oops! The 'Live Copy' button has been deprecated. You can now use Elementor's built-in 'Copy' feature instead. Enjoy the improved functionality!"
                            );
                        },
                    },
                    {
                        name: "ep-live-paste",
                        title: "Live Paste",
                        icon: "bdt-wi-element-pack",
                        callback: function () {
                            alert(
                                "Oops! The 'Live Paste' button has been deprecated. You can now use Elementor's built-in 'Paste from other site' feature instead. Enjoy the improved functionality!"
                            );
                        },
                    },
                ],
            });

            return groups;
        },
    };
    BdThemesLiveCopy.init();
})(window, document, jQuery);
