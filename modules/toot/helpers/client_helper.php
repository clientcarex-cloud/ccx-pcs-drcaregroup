<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function override_staff_select_branch_field($html, $field)
{
	log_message('debug', 'Custom field hook executed for field slug: ' . $field['slug']);

    if ($field['slug'] == 'staff_select_branch' && $field['fieldto'] == 'staff') {
        $field_id = 'custom_fields[' . $field['id'] . ']';

        $html = '<select id="' . $field_id . '" name="' . $field_id . '" class="form-control" data-custom-field-value="' . html_escape($field['value']) . '"></select>';
        $html .= '<script>
            $(function() {
                var select = $("#' . $field_id . '");
                $.ajax({
                    url: "' . admin_url('client/get_dynamic_options') . '",
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        select.empty();
                        $.each(data, function(i, item) {
                            select.append($("<option>").val(item.value).text(item.label));
                        });

                        var selected = select.attr("data-custom-field-value");
                        if (selected) {
                            select.val(selected);
                        }
                    },
                    error: function() {
                        console.error("Could not load dynamic options.");
                    }
                });
            });
        </script>';
    }

    return $html;
}
