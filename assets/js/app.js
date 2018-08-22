require('../js/sf-collection-ex-type');

$(document).ready(function(){
    $('.your-class').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 3
    });

    $('#wall_painting_postTags').select2({
        tags: true
    });


    $('.collection-ex-type').sfCollectionExType();
});

