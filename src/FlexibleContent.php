<?php

namespace Gwd\FlexibleContent;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Field;

class FlexibleContent extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'flexible-content';

    public function addLayout($layoutConfig)
    {
        $config = $this->normaliseLayoutConfig($layoutConfig);

        $layouts = (array_key_exists('layouts', $this->meta))
            ? $this->meta['layouts']
            : [];

        array_push($layouts, $config);

        return $this->withMeta([
            'layouts' => $layouts,
        ]);
    }
    public function addButtonText($text)
    {
        return $this->withMeta([
            'add_button_text' => $text
        ]);
    }
    public function displayStackedForm()
    {
        return $this->withMeta([
            'display_stacked' => true
        ]);
    }
    public function summaryLabel($label)
    {
        return $this->withMeta([
            'summary_label' => $label
        ]);
    }
    public function initialRows($count)
    {
        return $this->withMeta([
            'initial_rows' => $count
        ]);
    }
    public function maximumRows($count)
    {
        return $this->withMeta([
            'maximum_rows' => $count
        ]);
    }
    protected function fillAttributeFromRequest(NovaRequest $request,
                                                $requestAttribute,
                                                $model,
                                                $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = json_decode($request[$requestAttribute]);
        }
    }
    public function resolveAttribute($resource, $attribute = null)
    {
        $value = parent::resolveAttribute($resource, $attribute);
        return json_encode($value ?? []);
    }
    private function normaliseFieldConfig($field)
    {
        if (!empty($field['type'])) {
            if (
                $field['type'] == 'resource' &&
                !empty($field['resource']) &&
                !empty($field['resource_value']) &&
                !empty($field['resource_label'])
            ) {
                $o = !empty($field['resource_order']) ? $field['resource_order'] : 'asc';
                $s = !empty($field['resource_sortby']) ? $field['resource_sortby'] : 'id';

                $q = $field['resource']::orderBy($s, $o);

                if (!empty($field['resource_where'])) {
                    $q->where($field['resource_where']);
                }

                $field['options'] = $q->get();
            }
            return $field;
        }
        return false;
    }
    private function normaliseLayoutConfig($layoutConfig)
    {
        $allowedKeys = ['label', 'name', 'fields'];
        $config = array_intersect_key($layoutConfig, array_flip($allowedKeys));

        if (! isset($config['name'])) {
            $config['name'] = str_slug($config['label'], '_');
        }
        if (! isset($config['fields'])) {
            $config['fields'] = [];
        } else {
            $fields = [];
            foreach ($config['fields'] as $field) {
                $f = $this->normaliseFieldConfig($field);
                if ($f) {
                    $fields[] = $f;
                }
            }

            $config['fields'] = $fields;
        }
        return $config;
    }
}
