/**
 * Champ « Galerie du bien » de wp-admin : ouvre la médiathèque WordPress
 * pour choisir plusieurs photos, les affiche en vignettes réordonnables et
 * synchronise leurs identifiants dans un champ caché enregistré avec le bien.
 */
(function ($) {
    $(function () {
        $('[data-pp-galerie]').each(function () {
            var $wrap  = $(this);
            var $input = $wrap.find('.pp-galerie-ids');
            var $liste = $wrap.find('.pp-galerie-liste');
            var frame;

            function synchroniser() {
                var ids = [];
                $liste.children('li').each(function () {
                    ids.push($(this).attr('data-id'));
                });
                $input.val(ids.join(','));
            }

            $wrap.on('click', '.pp-galerie-ajouter', function (event) {
                event.preventDefault();

                if (frame) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: 'Photos du bien',
                    button: { text: 'Utiliser ces photos' },
                    library: { type: 'image' },
                    multiple: 'add'
                });

                frame.on('select', function () {
                    frame.state().get('selection').each(function (attachment) {
                        var media = attachment.toJSON();
                        if ($liste.find('li[data-id="' + media.id + '"]').length) {
                            return;
                        }
                        var apercu = (media.sizes && media.sizes.thumbnail)
                            ? media.sizes.thumbnail.url
                            : media.url;
                        $liste.append(
                            '<li data-id="' + media.id + '">' +
                                '<img src="' + apercu + '" alt="">' +
                                '<button type="button" class="pp-galerie-suppr" aria-label="Retirer cette photo">&times;</button>' +
                            '</li>'
                        );
                    });
                    synchroniser();
                });

                frame.open();
            });

            $wrap.on('click', '.pp-galerie-suppr', function (event) {
                event.preventDefault();
                $(this).closest('li').remove();
                synchroniser();
            });

            if ($.fn.sortable) {
                $liste.sortable({ items: '> li', update: synchroniser });
            }
        });
    });
})(jQuery);
