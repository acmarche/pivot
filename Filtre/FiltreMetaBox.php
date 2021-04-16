<?php


namespace AcMarche\Pivot\Filtre;

class FiltreMetaBox
{
    const KEY_NAME_HADES = 'hades_refrubrique';

    public function __construct()
    {
        add_action(
            'category_edit_form_fields',
            [$this, 'hades_metabox_edit'],
            10,
            1
        );
        add_action(
            'edited_category',
            [$this, 'save_hades_metadata'],
            10,
            1
        );
    }

    public static function hades_metabox_edit($tag)
    {
        $single = true;
        $term_id = $tag->term_id;
        $hades_refrubrique = get_term_meta($term_id, self::KEY_NAME_HADES, $single);
        ?>
        <table class="form-table">
            <tr class="form-field">
                <th scope="row" valign="top"><label for="bottin_refrubrique">Référence hades</label></th>
                <td>
                    <label>
                        <input type="text" name="hades_refrubrique" style="width: 100%;" autocomplete="off"
                               value="<?php echo $hades_refrubrique ?>">
                    </label>
                    <p class="description">Indiquer les références hades, séparées par une virgule</p>
                    <p class="description"><a href="/index-des-offres" target="_blank">Liste des références</a></p>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_hades_metadata($term_id)
    {
        $meta_key = self::KEY_NAME_HADES;

        if (isset($_POST[$meta_key]) && $_POST[$meta_key] != '') {
            $filtresString = $_POST[$meta_key];
            $filtres = explode(',', $filtresString);
            foreach ($filtres as $key => $filtre) {
                $filtre = trim($filtre);
                if ($filtre == '') {
                    unset($filtres[$key]);
                    continue;
                }
                $filtres[$key] = $filtre;
            }
            update_term_meta($term_id, $meta_key, join(',', $filtres));
        } else {
            delete_term_meta($term_id, $meta_key);
        }
    }

}
