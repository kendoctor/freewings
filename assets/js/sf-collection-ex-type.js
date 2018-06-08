(function($) {


    function addEntryForm(collectionContainer) {
        // Get the data-prototype explained earlier
        var prototype = collectionContainer.data('prototype');
        // get the new index
        var index = collectionContainer.data('index');

        var newForm = prototype;

        newForm = newForm.replace(/__name__/g, index);

        collectionContainer.data('index', index + 1);

        collectionContainer.append(newForm);

    }

    $.fn.sfCollectionExType = function(options) {
        // Establish our default settings
        var settings = $.extend({
        }, options);


        return this.each( function() {
            var collectionContainer = $(this);
            collectionContainer.data('index', collectionContainer.find('.collection-ex-type-entry').length);

        });
    }

    $(document).on('click', '[data-remove="collection-ex-type-entry"]', function(event) {
        event.preventDefault();
        $(event.target).closest('.collection-ex-type-entry').remove();
    });

    $(document).on('click', '[data-add="collection-ex-type-entry"]', function(event) {
        event.preventDefault();
        var collectionRow = $(event.target).closest('.collection-ex-type-row');
        var collectionContainer = $(collectionRow).find('.collection-ex-type');
        addEntryForm(collectionContainer)
    });

}(jQuery));