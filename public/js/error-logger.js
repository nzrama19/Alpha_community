/**
 * Script de capture des erreurs JavaScript côté client
 * Envoi automatique au serveur via API
 * 
 * À inclure dans chaque page:
 * <script src="/public/js/error-logger.js"></script>
 */

class ClientErrorLogger {
    constructor() {
        this.apiEndpoint = '/api/log-error.php';
        this.init();
    }

    /**
     * Initialiser les gestionnaires d'erreur
     */
    init() {
        // Capturer les erreurs JavaScript
        window.addEventListener('error', (event) => {
            this.logError({
                type: 'javascript',
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                stack: event.error ? event.error.stack : ''
            });
        });

        // Capturer les promesses rejetées
        window.addEventListener('unhandledrejection', (event) => {
            this.logError({
                type: 'javascript',
                message: 'Unhandled Promise Rejection: ' + (event.reason?.message || event.reason),
                stack: event.reason?.stack || ''
            });
        });

        // Capturer les erreurs de ressource (images, scripts, etc.)
        window.addEventListener('error', (event) => {
            if (event.target !== window) {
                this.logError({
                    type: 'javascript',
                    message: 'Resource Error: ' + event.target.src,
                    filename: event.target.src
                });
            }
        }, true);
    }

    /**
     * Enregistrer une erreur
     */
    logError(errorData) {
        // Ne pas logger les erreurs du logger lui-même
        if (errorData.filename && errorData.filename.includes('error-logger.js')) {
            return;
        }

        // Envoyer au serveur
        this.sendToServer(errorData);

        // Log en console (development)
        if (this.isDevelopment()) {
            console.error('[CLIENT ERROR]', errorData);
        }
    }

    /**
     * Envoyer l'erreur au serveur
     */
    sendToServer(errorData) {
        try {
            fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(errorData)
            }).catch(err => {
                // Silencieusement ignorer les erreurs d'envoi
                console.warn('Failed to send error to server:', err);
            });
        } catch (err) {
            // Si fetch n'existe pas, utiliser XMLHttpRequest
            this.sendViaXHR(errorData);
        }
    }

    /**
     * Fallback avec XMLHttpRequest pour les navigateurs anciens
     */
    sendViaXHR(errorData) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', this.apiEndpoint, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(errorData));
    }

    /**
     * Vérifier si on est en development
     */
    isDevelopment() {
        return window.location.hostname === 'localhost' || 
               window.location.hostname === '127.0.0.1';
    }

    /**
     * Logger un avertissement personnalisé
     */
    warn(message, context = {}) {
        this.logError({
            type: 'warning',
            message: message,
            context: context
        });
    }

    /**
     * Logger une information personnalisée
     */
    info(message, context = {}) {
        this.logError({
            type: 'info',
            message: message,
            context: context
        });
    }
}

// Initialiser automatiquement
const errorLogger = new ClientErrorLogger();

// Exposer globalement
window.ErrorLogger = errorLogger;
