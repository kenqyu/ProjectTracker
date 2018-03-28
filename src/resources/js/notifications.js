/**
 * Created by alex on 11/4/16.
 */

class Notifications {
    constructor() {
        this.initSidebar();
        this.initLinksToSidebar();
        this.initBinds();
    }

    initSidebar() {
        /*$('body').click(function (e) {
         if ($('body').hasClass('sidebar-open') && e.target.id != 'sidebar') {
         e.preventDefault();
         Notifications.closeSidebar();
         }
         });*/
        $('#sidebar button.close').click(function (e) {
            e.preventDefault();
            Notifications.closeSidebar();
        });
    }

    static openSidebar() {
        $('body').addClass('sidebar-open');
    }

    static closeSidebar() {
        $('body').removeClass('sidebar-open');
    }

    initLinksToSidebar() {
        $('a.notifications-link').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            Notifications.openSidebar();
        });
    }

    initBinds() {
        $('#sidebar').find('.item a').click(function (e) {
            e.preventDefault();
            window.location = '/notifications/notifications/mark-as-read?id=' + $(this).closest('.item').data('id');
        });
        $('#sidebar .mark_as_read').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            $.get($(this).attr('href')).done(function () {
                $('#sidebar .new').remove();
                $('.notifications-link span').remove();
            });
        });
    }
}
new Notifications();