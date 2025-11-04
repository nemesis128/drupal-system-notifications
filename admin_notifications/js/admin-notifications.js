/**
 * @file
 * Script principal para el sistema de polling de notificaciones.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.adminNotifications = {
    lastCheck: 0,
    pollTimer: null,
    pollInterval: 30000, // 30 segundos por defecto

    attach: function (context, settings) {
      const self = this;

      // Solo ejecutar una vez
      if (context !== document) {
        return;
      }

      // Verificar que el módulo esté habilitado
      if (!settings.adminNotifications || !settings.adminNotifications.enabled) {
        return;
      }

      // Configurar intervalo de polling
      if (settings.adminNotifications.pollInterval) {
        this.pollInterval = parseInt(settings.adminNotifications.pollInterval, 10);
      }

      // Inicializar timestamp - comenzar desde hace 5 minutos para capturar
      // notificaciones recientes que podrían haberse creado antes de cargar la página
      const fiveMinutesAgo = Math.floor(Date.now() / 1000) - 300;
      this.lastCheck = fiveMinutesAgo;

      // Iniciar polling
      this.startPolling();

      // Manejar cierre de banners
      $(document).on('click', '[data-dismiss-notification]', function (e) {
        e.preventDefault();
        const notificationId = $(this).data('dismiss-notification');
        self.dismissBanner(notificationId, $(this).closest('.admin-notification-banner'));
      });
    },

    startPolling: function () {
      const self = this;

      // Hacer el primer check inmediatamente
      this.checkNotifications();

      // Luego continuar con el intervalo configurado
      this.pollTimer = setInterval(function () {
        self.checkNotifications();
      }, this.pollInterval);
    },

    checkNotifications: function () {
      const self = this;

      $.ajax({
        url: '/admin-notifications/poll',
        method: 'GET',
        data: {
          last_check: this.lastCheck
        },
        dataType: 'json',
        success: function (response) {
          if (response.notifications && response.notifications.length > 0) {
            response.notifications.forEach(function (notification) {
              self.showToastNotification(notification);
            });
          }

          // Actualizar el timestamp del último check
          if (response.timestamp) {
            self.lastCheck = response.timestamp;
          }
        },
        error: function (xhr, status, error) {
          console.error('Error polling notifications:', error);
        }
      });
    },

    showToastNotification: function (notification) {
      if (typeof Drupal.toastNotifications !== 'undefined') {
        // Usar la duración configurada del módulo toast
        const duration = Drupal.toastNotifications.toastDuration;
        Drupal.toastNotifications.show(
          notification.title,
          notification.message,
          notification.severity,
          duration
        );
      }
    },

    dismissBanner: function (notificationId, $banner) {
      // Marcar como leída en el servidor
      $.ajax({
        url: '/admin-notifications/mark-read/' + notificationId,
        method: 'POST',
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            // Animar y remover el banner
            $banner.slideUp(300, function () {
              $(this).remove();
            });
          }
        },
        error: function (xhr, status, error) {
          console.error('Error dismissing notification:', error);
          // Aún así remover visualmente
          $banner.slideUp(300, function () {
            $(this).remove();
          });
        }
      });
    },

    detach: function (context, settings, trigger) {
      // Limpiar el timer cuando se desmonta
      if (trigger === 'unload' && this.pollTimer) {
        clearInterval(this.pollTimer);
        this.pollTimer = null;
      }
    }
  };

})(jQuery, Drupal, drupalSettings);
