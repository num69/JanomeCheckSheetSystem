import {
    animate,
    stagger,
    spring,
} from "animejs";

animate(".page-animated", {
    opacity: {
        from: 0,
        to: 1
    },
    translateY: {
        from: 12,
        to: 0,
    },
    duration: 250,
    delay: stagger(10),
    ease: "outQuad",
    onComplete: function () {
        // Remove the page-hidden-overflow class from the body after the animation is complete
        $("body").removeClass("page-hidden-overflow");
    }
});

window.appAnimation = {

    createAnimate: animate,

    showSummaryCards: function () {
        animate(".summary-card", {
            opacity: {
                from: 0,
                to: 1
            },
            translateY: {
                from: 20,
                to: 0,
            },
            duration: 500,
            delay: stagger(200),
            ease: "outQuad"
        });
    },

    showTableRows: function () {
        animate("tbody tr", {
            opacity: {
                from: 0
            },
            translateY: {
                from: 8
            },
            duration: 300,
            delay: stagger(25),
            ease: "outQuad"
        });
    },

    animateNumber: function (selector, value) {
        var targetValue = parseFloat(value) || 0;

        var counter = {
            value: 0
        };

        animate(counter, {
            value: targetValue,
            duration: 700,
            ease: "outExpo",

            onUpdate: function () {
                $(selector).text(
                    Math.round(counter.value).toLocaleString("en-US")
                );
            }
        });
    },

    sidebarMenuAnimation: function () {
        var menuItems = document.querySelectorAll(
            ".sidebar-menu-animated > .nav-link, " +
            ".sidebar-menu-animated > .sidebar-heading, " +
            ".sidebar-menu-animated > .sidebar-submenu-item"
        );

        animate(menuItems, {
            opacity: {
                from: 0,
                to: 1
            },
            translateY: {
                from: 8,
                to: 0
            },
            duration: 300,
            delay: stagger(40),
            ease: "outQuad"
        });
    },

    showSubmenuItems: function (submenu) {
        var links = submenu.querySelectorAll(
            ".sidebar-submenu-link"
        );

        submenu.style.display = "block";
        submenu.style.height = "auto";
        submenu.style.overflow = "hidden";

        var targetHeight = submenu.scrollHeight;

        submenu.style.height = "0px";

        animate(submenu, {
            height: {
                from: 0,
                to: targetHeight
            },
            duration: 300,
            ease: "outCubic",
            onComplete: function () {
                submenu.style.height = "auto";
                submenu.style.overflow = "";
            }
        });

        animate(links, {
            opacity: {
                from: 0,
                to: 1
            },
            translateX: {
                from: -10,
                to: 0
            },
            duration: 250,
            delay: stagger(50),
            ease: "outQuad"
        });
    },

    hideSidebarSubmenu: function (submenu, onComplete) {
        submenu.style.height = submenu.scrollHeight + "px";
        submenu.style.overflow = "hidden";

        animate(submenu, {
            height: 0,
            opacity: {
                from: 1,
                to: 0
            },
            duration: 250,
            ease: "inCubic",
            onComplete: function () {
                submenu.style.display = "none";
                submenu.style.opacity = "";
                submenu.style.height = "0px";

                if (typeof onComplete === "function") {
                    onComplete();
                }
            }
        });
    },

    datepickerAnimation: null,

    showDatepicker: function (element) {
        if (this.datepickerAnimation) {
            this.datepickerAnimation.cancel();
        }

        element.style.transformOrigin = "center top";

        this.datepickerAnimation = animate(element, {
            opacity: {
                from: 0,
                to: 1
            },
            rotateX: {
                from: -90,
                to: 0
            },
            ease: spring({
                mass: 1.3,
                stiffness: 80,
                damping: 5,
                velocity: 0
            })
        });

        return this.datepickerAnimation;
    },

    hideDatepicker: function (element, onComplete) {
        if (this.datepickerAnimation) {
            this.datepickerAnimation.cancel();
        }

        this.datepickerAnimation = animate(element, {
            opacity: {
                from: 1,
                to: 0
            },
            rotateX: {
                from: 0,
                to: -90
            },
            duration: 300,
            ease: "outCubic",

            onComplete: function () {
                if (typeof onComplete === "function") {
                    onComplete();
                }
            }
        });

        return this.datepickerAnimation;
    }
};
