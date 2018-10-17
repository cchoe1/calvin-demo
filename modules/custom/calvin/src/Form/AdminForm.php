<?php

namespace Drupal\calvin\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\user\PrivateTempStoreFactory;
use Drupal\Core\Url;

/**
 * Class AdminForm.
 */
class AdminForm extends FormBase {

  public function __construct(
    PrivateTempStoreFactory $private_temp_store
  ) {
    $this->tempStore = $private_temp_store;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $session = $this->tempStore->get('calvin');

    $form['#attached']['drupalSettings']['calvin'] = [
      'title' => $session->get('title') ?? '',
      'body' => $session->get('body') ?? '',
      'file_uri' => $session->get('file_uri') ?? ''
    ];

    $form['fields'] = [
      '#type' => 'details',
      '#title' => $this->t("Fields"),
      '#open' => TRUE,
    ];

    $form['fields']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Title"),
      '#description' => $this->t("A nice, wholesome title")
    ];

    $form['fields']['body'] = [
      '#type' => 'text_format',
      '#title' => $this->t("Body"),
    ];

    $form['fields']['file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('File'),
      '#description' => $this->t('(pdf only)'),
      '#upload_location' => 'private://pdf',
      '#upload_validators' => [
        'file_validate_extensions' => ['pdf'],
      ]
    ];

    $form['redirect_back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Redirect Back'),
      '#submit' => ['::submitForm']
    ];
    $form['redirect_template'] = [
      '#type' => "submit",
      '#value' => $this->t("Redirect to Template"),
      '#submit' => ['::redirectToTemplate']
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $session = $this->tempStore->get('calvin');
    $title = $form_state->getValue('title');
    $body = $form_state->getValue('body');

    $fid = $form_state->getValue(['file', 0]);

    if ($fid) {

      $file = File::load($fid);
      $file->setTemporary();
      $file->save();
      $file_uri = $file->getFileUri();
    }


    $session->set('title', $title ?? '');
    $session->set('body', $body ?? '');
    $session->set('file_uri', $file_uri ?? '');

    return $form;
  }

  /**
   * Redirects to template controlled by Drupal\calvin\Controller\AdminFormViewController::view
   *
   * @return RedirectResponse
   */
  public function redirectToTemplate(array &$form, FormStateInterface $form_state) {
    $fid = $form_state->getValue(['file', 0]) ?? '';

    if ($fid) {
      $file = File::load($fid);
      $file->setTemporary();
      $file->save();
      $file_uri = $file->getFileUri();
    }

    $title = $form_state->getValue('title');
    $body = $form_state->getValue('body');

    $param['param'] = [
      'title' => $title ?? '',
      'body' => $body ?? '',
      'file_uri' => $file_uri ?? ''
    ];
    $url = Url::fromRoute('calvin.admin_form_view_controller_view', $param);

    $form_state->setRedirectUrl($url);
    return $form_state;

  }

}
