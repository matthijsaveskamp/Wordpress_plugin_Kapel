jQuery(document).ready(function($) {
    var galleryFrame;
    
    // Open media library voor het selecteren van meerdere foto's
    $('#kfg-add-images').on('click', function(e) {
        e.preventDefault();
        
        // Als de media frame al bestaat, heropen deze
        if (galleryFrame) {
            galleryFrame.open();
            return;
        }
        
        // Creëer een nieuwe media frame
        galleryFrame = wp.media({
            title: 'Selecteer Foto\'s voor de Footer Gallery',
            button: {
                text: 'Voeg toe aan gallery'
            },
            multiple: true,
            library: {
                type: 'image'
            }
        });
        
        // Wanneer foto's zijn geselecteerd
        galleryFrame.on('select', function() {
            var selection = galleryFrame.state().get('selection');
            
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                
                // Check of de foto niet al is toegevoegd
                if ($('.kfg-image-preview[data-id="' + attachment.id + '"]').length > 0) {
                    return;
                }
                
                // Voeg preview toe
                var imagePreview = $('<div class="kfg-image-preview" data-id="' + attachment.id + '"></div>');
                imagePreview.append('<img src="' + attachment.sizes.thumbnail.url + '" />');
                imagePreview.append('<span class="kfg-remove-image">&times;</span>');
                imagePreview.append('<input type="hidden" name="kfg_gallery_images[]" value="' + attachment.id + '" />');
                imagePreview.append('<input type="text" class="kfg-caption-input" name="kfg_image_captions[' + attachment.id + ']" value="" placeholder="Optionele tekst..." />');
                
                $('#kfg-selected-images').append(imagePreview);
            });
        });
        
        // Open de media frame
        galleryFrame.open();
    });
    
    // Verwijder foto uit gallery
    $(document).on('click', '.kfg-remove-image', function() {
        $(this).closest('.kfg-image-preview').fadeOut(300, function() {
            $(this).remove();
        });
    });
    
    // Maak de foto's sorteerbaar met drag & drop
    $('#kfg-selected-images').sortable({
        placeholder: 'kfg-image-placeholder',
        cursor: 'move',
        tolerance: 'pointer'
    });
});
