const CONFIG = {
    get basePath() {
        const path = window.location.pathname;
        const partes = path.split('/');
        if (partes.length > 1 && partes[1] !== '' && !partes[1].includes('.')) {
            return '/' + partes[1];
        }
        return '';
    },
    
    get baseURL() {
        return window.location.origin + this.basePath;
    }
};