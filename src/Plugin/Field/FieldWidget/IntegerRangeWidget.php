<?php

/**
 * @file
 * Contains \Drupal\range_units\Plugin\Field\FieldWidget\TextWidget.
 */

namespace Drupal\range_units\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Plugin implementation of the 'field_temperature_range' widget.
 *
 * @FieldWidget(
 *   id = "temperature_range",
 *   module = "temperature",
 *   label = @Translation("Temperature"),
 *   field_types = {
 *     "integer_range"
 *   }
 * )
 */
class IntegerRangeWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $from = isset($items[$delta]->from) ? $items[$delta]->from : NULL;
    $to = isset($items[$delta]->to) ? $items[$delta]->to : NULL;
    $unit = isset($items[$delta]->unit) ? $items[$delta]->unit : NULL;
    $setting = $this->getFieldSetting('unit');
    $unit_array = explode(',', $setting);
    foreach ($unit_array as $value) {
      $unit_option[$value] = $value;
    }
    // Wrap in a fieldset for single field.
    if ($this->fieldDefinition->getFieldStorageDefinition()->getCardinality() === 1) {
      $element['#type'] = 'fieldset';
    }

    $base = array(
      '#type' => 'number',
      '#required' => $element['#required'],
    );

    // Set the step for floating point and decimal numbers.
    switch ($this->fieldDefinition->getType()) {
      case 'range_decimal':
        $base['#step'] = pow(0.1, $this->getFieldSetting('scale'));
        break;

      case 'range_float':
        $base['#step'] = 'any';
        break;
    }

    // Set minimum and maximum.
    if (is_numeric($this->getFieldSetting('min'))) {
      $base['#min'] = $this->getFieldSetting('min');
    }
    if (is_numeric($this->getFieldSetting('max'))) {
      $base['#max'] = $this->getFieldSetting('max');
    }

    $element['from'] = array(
      '#title' => t('From'),
      '#default_value' => $from,
      '#prefix' => '<div class="field--widget-range-text-fields clearfix">',
        ) + $base;

    $element['to'] = array(
      '#title' => t('to'),
      '#default_value' => $to,
      '#suffix' => '</div>',
        ) + $base;

    $element['unit'] = array(
      '#type' => 'select',
      '#required' => $element['#required'],
      '#default_value' => $unit,
      '#title' => t('unit'),
      '#options' => $unit_option,
      '#suffix' => '</div>',
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return $element;
  }

}
