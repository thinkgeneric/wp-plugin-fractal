(function( window, document, $ ) {
    'use strict';

    var factual = {};

    factual.cache = function() {
        factual.$factualForm = $('#factual-importer');
        factual.$dataTable = $('.js-factual-results');
    };

    factual.init = function() {
        factual.cache();
        factual.assignListener();
    };

    factual.assignListener = function() {

        factual.$factualForm.submit(function(event){
            event.preventDefault();
            var data =  $(event.currentTarget).serialize();

            //destroy the table
            // factual.$dataTable.destroy();

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: data,
                success: function(response) {
                    factual.renderTable( factual.prepareTableData( response ) );
                },
                dataType: 'json'
            });

        });
    };

    factual.renderTable = function( data ) {
        factual.$dataTable.DataTable({
            data: $.parseJSON(data),
            deferRender: true,
            destroy: true,
            columns: [
                {
                    "orderable":      false,
                    "data":           null,
                    "defaultContent": ""
                },
                { data: 'factual_id' },
                { data: 'category_labels' },
                { data: 'name' },
            ],
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            } ],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            order: [[ 1, 'asc' ]],
            dom: 'Bfrtip',
            buttons: [
                {
                    'extend': 'selected',
                    'text': 'Import Selected',
                    'action': function(e, dt) {
                        var rows = dt.rows({selected:true}).data().toArray();
                        var rowsJson = JSON.stringify( rows );

                        factual.submitImport(rows);
                    }
                },
                'selectAll',
                'selectNone',
            ],
        });
    };

    factual.prepareTableData = function( response ) {

        return response.data;
    };

    factual.submitImport = function( dtRows ) {


        var data = {
            'import-listing': factualAjax.factualImportNonce,
            'action': 'import_factual_data',
            'import-rows': dtRows
        };

        console.log( data );
        $.ajax({
            type: 'post',
            url: factualAjax.ajaxurl,
            data: data,
            success: function(response) {
                console.log(response);
            },
            dataType: 'json'
        });
    };

    $(document).ready(factual.init);

    return factual;

    // $( document).ready( function( $ ) {
    //     $( '.upcomming-data-table table' ).DataTable();
    //     $( '.rows-data-table table' ).DataTable();
    //
    //     var data = {
    //         'action': 'get_factual_data'
    //     };
    //
    //     $.post(ajaxurl, data, function( response ) {
    //        console.log( response );
    //     });
    // });

})( window, document, jQuery );