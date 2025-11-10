/*! DataTables Bootstrap 5 integration
 * © SpryMedia Ltd - datatables.net/license
 */

(function( factory ){
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( ['jquery', 'datatables.net'], function ( $ ) {
			return factory( $, window, document );
		} );
	}
	else if ( typeof exports === 'object' ) {
		// CommonJS
		var jq = require('jquery');
		var cjsRequires = function (root, $) {
			if ( ! $.fn.dataTable ) {
				require('datatables.net')(root, $);
			}
		};

		if (typeof window === 'undefined') {
			module.exports = function (root, $) {
				if ( ! root ) {
					// CommonJS environments without a window global must pass a
					// root. This will give an error otherwise
					root = window;
				}

				if ( ! $ ) {
					$ = jq( root );
				}

				cjsRequires( root, $ );
				return factory( $, root, root.document );
			};
		}
		else {
			cjsRequires( window, jq );
			module.exports = factory( jq, window, window.document );
		}
	}
	else {
		// Browser
		factory( jQuery, window, document );
	}
}(function( $, window, document ) {
'use strict';
var DataTable = $.fn.dataTable;

/**
 * DataTables integration for Bootstrap 5.
 *
 * This file sets the defaults and adds options to DataTables to style its
 * controls using Bootstrap. See https://datatables.net/manual/styling/bootstrap
 * for further information.
 */

/* Set the defaults for DataTables initialisation */
$.extend( true, DataTable.defaults, {
	renderer: 'bootstrap'
} );


/* Default class modification */
$.extend( true, DataTable.ext.classes, {
	container: "dt-container dt-bootstrap5",
	search: {
		input: "form-control"
	},
	length: {
		select: "form-select"
	},
	processing: {
		// container: "dt-processing-red-blue"
		container: ""
	}
} );

$(document).off('input', '.searchInputTable').on('input', '.searchInputTable', function(event) {
    let $this = $(this);
    clearTimeout(debounceTimeout);
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if(currentValue.length === 0) {
        $(this).closest('.form-group').find('button.searchButtonClearTable').addClass('d-none');
        debounceTimeout = setTimeout(function () {
            if (isCleared == 'false') {
                let tableId = $this.closest('.table-container').find('table[id]').attr('id');
                if ( $.fn.DataTable.isDataTable('#' + tableId) ) {
                    $('#' + tableId).DataTable().ajax.reload();
                }
            }
        }, 1000);

        $(this).attr('data-iscleared', 'true');
    }
    else {
        $(this).closest('.form-group').find('button.searchButtonClearTable').removeClass('d-none');
        $(this).attr('data-iscleared', 'false');
    }
});

$(document).off('click', '.searchButtonClearTable').on('click', '.searchButtonClearTable', function(event) {
    let $this = $(this);
    let isCleared = $(this).closest('.form-group').find('input.searchInputTable').attr('data-iscleared');
    $(this).closest('.form-group').find('input.searchInputTable').val('');
    $(this).closest('.form-group').find('input.searchInputTable').focus();
    $(this).addClass('d-none');

    if (isCleared == 'false') {
        let tableId = $this.closest('.table-container').find('table[id]').attr('id');
		if ( $.fn.DataTable.isDataTable('#' + tableId) ) {
			$('#' + tableId).DataTable().ajax.reload();
		}
    }

    $(this).closest('.form-group').find('input.searchInputTable').attr('data-iscleared', 'true');
});

$(document).off('click', '.searchButtonTable').on('click', '.searchButtonTable', function(event) {
    let $this = $(this);
    let isCleared = $(this).closest('.form-group').find('input.searchInputTable').attr('data-iscleared');
    if (isCleared == 'false') {
        let tableId = $this.closest('.table-container').find('table[id]').attr('id');
		if ( $.fn.DataTable.isDataTable('#' + tableId) ) {
			$('#' + tableId).DataTable().ajax.reload();
		}
    }
});

$(document).off('keydown', '.searchInputTable').on('keydown', '.searchInputTable', function(event) {
    const $this = $(this);
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if (event.key === 'Enter' || event.keyCode === 13) {
        event.preventDefault();
        if ((currentValue.length === 0 && isCleared == 'false') || currentValue.length > 0) {
            let tableId = $this.closest('.table-container').find('table[id]').attr('id');
			if ( $.fn.DataTable.isDataTable('#' + tableId) ) {
				$('#' + tableId).DataTable().ajax.reload();
			}

            if(currentValue.length === 0 && isCleared == 'false') {
                $(this).attr('data-iscleared', 'true');
            }
            else {
                $(this).attr('data-iscleared', 'false');
            }
        }
    }
});

/* Bootstrap paging button renderer */
DataTable.ext.renderer.pagingButton.bootstrap = function (settings, buttonType, content, active, disabled) {
	var btnClasses = ['dt-paging-button', 'page-item'];

	if (active) {
		btnClasses.push('active');
	}

	if (disabled) {
		btnClasses.push('disabled')
	}

	var li = $('<li>').addClass(btnClasses.join(' '));
	var a = $('<a>', {
		'href': disabled ? null : '#',
		'class': 'page-link'
	})
		.html(content)
		.appendTo(li);

	return {
		display: li,
		clicker: a
	};
};

DataTable.ext.renderer.pagingContainer.bootstrap = function (settings, buttonEls) {
	return $('<ul/>').addClass('pagination').append(buttonEls);
};

DataTable.ext.renderer.layout.bootstrap = function ( settings, container, items ) {
	var row = $( '<div/>', {
			"class": items.full ?
				'row mt-2 justify-content-md-center' :
				'row mt-2 justify-content-between'
		} )
		.appendTo( container );

	$.each( items, function (key, val) {
		var klass;

		// Apply start / end (left / right when ltr) margins
		if (val.table) {
			klass = 'col-12';
		}
		else if (key === 'start') {
			klass = 'col-md-auto me-auto';
		}
		else if (key === 'end') {
			klass = 'col-md-auto ms-auto';
		}
		else {
			klass = 'col-md';
		}

		$( '<div/>', {
				id: val.id || null,
				"class": klass + ' ' + (val.className || '')
			} )
			.append( val.contents )
			.appendTo( row );
	} );
};


return DataTable;
}));
