/* Messages de Select2 en français (chargé juste après select2.min.js) */
if (window.jQuery && jQuery.fn.select2) {
    jQuery.fn.select2.defaults.set('language', {
        noResults: function () { return 'Aucun résultat'; },
        searching: function () { return 'Recherche…'; },
        loadingMore: function () { return 'Chargement…'; },
        errorLoading: function () { return 'Impossible de charger les résultats'; },
        removeAllItems: function () { return 'Tout retirer'; },
        inputTooShort: function (a) { var n = a.minimum - a.input.length; return 'Saisissez au moins ' + n + ' caractère' + (n > 1 ? 's' : ''); },
        inputTooLong: function (a) { var n = a.input.length - a.maximum; return 'Supprimez ' + n + ' caractère' + (n > 1 ? 's' : ''); },
        maximumSelected: function (a) { return 'Vous ne pouvez sélectionner que ' + a.maximum + ' élément' + (a.maximum > 1 ? 's' : ''); }
    });
}
