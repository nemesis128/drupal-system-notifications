/**
 * @file
 * Sistema de notificaciones Toast estilo Windows.
 */

(function (Drupal, drupalSettings) {
  'use strict';

  /**
   * Gestor de notificaciones toast.
   */
  Drupal.toastNotifications = {
    container: null,
    toasts: [],
    maxToasts: 5,
    toastDuration: 10000, // 10 segundos por defecto
    position: 'bottom-right',
    soundEnabled: true,

    /**
     * Inicializa el contenedor de toasts.
     */
    init: function () {
      if (this.container) {
        return;
      }

      // Configuración desde drupalSettings
      if (drupalSettings.adminNotifications) {
        const config = drupalSettings.adminNotifications;
        if (config.toast_duration) {
          this.toastDuration = parseInt(config.toast_duration, 10);
        }
        if (config.toast_position) {
          this.position = config.toast_position;
        }
        if (typeof config.sound_enabled !== 'undefined') {
          this.soundEnabled = config.sound_enabled;
        }
      }

      // Crear contenedor
      this.container = document.createElement('div');
      this.container.className = 'toast-notifications-container toast-notifications-container--' + this.position;
      this.container.setAttribute('role', 'region');
      this.container.setAttribute('aria-label', 'Notificaciones');
      document.body.appendChild(this.container);
    },

    /**
     * Muestra una notificación toast.
     *
     * @param {string} title - Título de la notificación.
     * @param {string} message - Mensaje de la notificación.
     * @param {string} severity - Tipo de severidad (info, success, warning, error).
     * @param {number} duration - Duración en milisegundos (opcional).
     */
    show: function (title, message, severity, duration) {
      this.init();

      severity = severity || 'info';
      duration = duration || this.toastDuration;

      // Limitar el número de toasts
      if (this.toasts.length >= this.maxToasts) {
        this.close(this.toasts[0]);
      }

      // Crear elemento toast
      const toast = this.createToast(title, message, severity);
      this.container.appendChild(toast);
      this.toasts.push(toast);

      // Reproducir sonido si está habilitado
      if (this.soundEnabled) {
        this.playNotificationSound();
      }

      // Animar entrada
      setTimeout(function () {
        toast.classList.add('toast-notification--visible');
      }, 10);

      // Auto-cerrar después de la duración
      const self = this;
      setTimeout(function () {
        self.close(toast);
      }, duration);

      return toast;
    },

    /**
     * Crea el elemento DOM del toast.
     */
    createToast: function (title, message, severity) {
      const toast = document.createElement('div');
      toast.className = 'toast-notification toast-notification--' + severity;
      toast.setAttribute('role', 'alert');
      toast.setAttribute('aria-live', 'assertive');
      toast.setAttribute('aria-atomic', 'true');

      const icon = this.getIcon(severity);

      toast.innerHTML = `
        <div class="toast-notification__icon">
          ${icon}
        </div>
        <div class="toast-notification__content">
          <div class="toast-notification__title">${this.escapeHtml(title)}</div>
          <div class="toast-notification__message">${this.escapeHtml(message)}</div>
        </div>
        <button class="toast-notification__close" aria-label="Cerrar notificación">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      `;

      // Agregar evento de cerrar
      const self = this;
      const closeButton = toast.querySelector('.toast-notification__close');
      closeButton.addEventListener('click', function (e) {
        e.preventDefault();
        self.close(toast);
      });

      return toast;
    },

    /**
     * Cierra una notificación toast.
     */
    close: function (toast) {
      if (!toast || !toast.parentNode) {
        return;
      }

      toast.classList.remove('toast-notification--visible');
      toast.classList.add('toast-notification--closing');

      const self = this;
      setTimeout(function () {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
        const index = self.toasts.indexOf(toast);
        if (index > -1) {
          self.toasts.splice(index, 1);
        }
      }, 300);
    },

    /**
     * Obtiene el icono SVG según la severidad.
     */
    getIcon: function (severity) {
      const icons = {
        error: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>`,
        warning: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
          <line x1="12" y1="9" x2="12" y2="13"></line>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>`,
        success: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>`,
        info: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>`
      };

      return icons[severity] || icons.info;
    },

    /**
     * Reproduce un sonido de notificación.
     */
    playNotificationSound: function () {
      // Crear un tono simple usando Web Audio API
      try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = 800;
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
      } catch (e) {
        // Silenciar errores si Web Audio API no está disponible.
      }
    },

    /**
     * Escapa HTML para prevenir XSS.
     */
    escapeHtml: function (text) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        '\'': '&#039;'
      };
      return text.replace(/[&<>"']/g, function (m) {
        return map[m];
      });
    }
  };

  // Inicializar en document ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      Drupal.toastNotifications.init();
    });
  } else {
    Drupal.toastNotifications.init();
  }

})(Drupal, drupalSettings);
