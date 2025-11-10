(function($) {
    $.fn.autosize = function(options) {
        // Default settings
        var settings = $.extend({
            append: "",         // Default append value
            initialHeight: '50px' // Default initial height for textareas
        }, options);

        // Iterate over each matched element
        return this.each(function() {
            var $textarea = $(this);
            var maxRows = parseInt($textarea.attr('rows')) || Infinity; // Batas jumlah baris, default tanpa batas

            // Set the initial height based on the settings
            $textarea.css('height', settings.initialHeight);

            // Function to resize the textarea
            function resize() {
                // Reset height to auto to get the correct scrollHeight
                $textarea.css('height', 'auto');

                // Set the height to scrollHeight + append (if provided)
                $textarea.css('height', $textarea[0].scrollHeight + getAppendHeight(settings.append) + 'px');
            }

            // Calculate extra height based on append (e.g., if newline is appended)
            function getAppendHeight(append) {
                if (append === "\n") {
                    return 20; // Adjust height for newline if required
                }
                return 0; // Default no extra height if append is empty
            }

            // Check the number of lines and prevent exceeding the limit
            function limitRows() {
                var lineCount = $textarea.val().split('\n').length;

                // Jika limit baris telah tercapai, potong teks
                if (lineCount > maxRows) {
                    var lines = $textarea.val().split('\n').slice(0, maxRows);
                    $textarea.val(lines.join('\n'));
                }
            }

            // Initially resize the textarea after setting the initial height
            resize();

            // Bind resize and limitRows on input event
            $textarea.on('input', function() {
                // Batasi jumlah baris jika data-limit-rows=true
                if ($textarea.data('limit-rows')) {
                    limitRows();
                }
                resize();
            });

            $textarea.on('change', function() {
                // Batasi jumlah baris jika data-limit-rows=true
                if ($textarea.data('limit-rows')) {
                    limitRows();
                }
                resize();
            });

            // Prevent Enter key if rows exceed limit
            $textarea.on('keydown', function(e) {
                if ($textarea.data('limit-rows') && e.key === 'Enter') {
                    var lineCount = $textarea.val().split('\n').length;
                    if (lineCount >= maxRows) {
                        e.preventDefault(); // Cegah Enter jika baris sudah mencapai limit
                    }
                }
            });
        });
    };
}(jQuery));