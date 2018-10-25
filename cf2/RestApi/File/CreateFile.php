<?php


namespace calderawp\calderaforms\cf2\RestApi\File;


use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader;
use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\cf2\Fields\Handlers\UploaderContract;

class CreateFile extends File
{

    /** @inheritdoc */
    protected function getArgs()
    {
        return [

            'methods' => 'POST',
            'callback' => [$this, 'createItem'],
            'permission_callback' => [$this, 'permissionsCallback' ],
            'args' => [
                'hashes' => [
                    'description' => __('MD5 has of files to upload, should have same order as files arg', 'caldera-forms')
                ],
                'verify' => [
                    'type' => 'string',
                    'description' => __('Verification token (nonce) for form', 'caldera-forms'),
                    'required' => true,
                ],
                'formId' => [
                    'type' => 'string',
                    'description' => __('ID for form field belongs to', 'caldera-forms'),
                    'required' => true,
                ],
                'fieldId' => [
                    'type' => 'string',
                    'description' => __('ID for field files are submitted to', 'caldera-forms'),
                    'required' => true,
                ],
                'control' => [
                    'type' => 'string',
                    'description' => __('Unique control string for field', 'caldera-forms'),
                    'required' => true,
                ]
            ]
        ];
    }

    /**
     * Permissions check for file field uploads
     *
     * @since 1.8.0
     *
     * @param \WP_REST_Request $request Request object
     *
     * @return bool
     */
    public function permissionsCallback(\WP_REST_Request $request ){
        $form =  \Caldera_Forms_Forms::get_form($request->get_param( 'formId' ) );
        if( FileFieldType::getCf1Identifier() !== \Caldera_Forms_Field_Util::get_type( $request->get_param( 'fieldId' ), $form ) ){
            return false;
        }

        return \Caldera_Forms_Render_Nonce::verify_nonce(
            $request->get_param( 'verify'),
            $request->get_param( 'formId' )
        );
    }

    /**
     * Upload file for cf2 file field
     *
     * @since 1.8.0
     *
     * @param \WP_REST_Request $request
     * @return mixed|\WP_Error|\WP_REST_Response
     */
    public function createItem(\WP_REST_Request $request)
    {

        $files = $request->get_file_params();
        $formId = $request->get_param('formId');
        $this->setFormById($formId);
        $fieldId = $request->get_param('fieldId');
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$this->getForm());
        $uploader = new Cf1FileUploader();
        if( is_wp_error( $uploader ) ){
            /** @var \WP_Error $uploader */
            return new \WP_REST_Response(['message' => $uploader->get_error_message() ],$uploader->get_error_code() );
        }
        $hashes = $request->get_param( 'hashes');
        $controlCode = $request->get_param( 'control'  );
        $handler = new FileUpload(
            $field,
            $this->getForm(),
            new Cf1TransientsApi(),
            new Cf1FileUploader()
        );
        try{
            $controlCode = $handler->processFiles($files,$hashes,$controlCode);
        }catch( \Exception $e ){
            return new \WP_REST_Response(['message' => $e->getMessage() ],$e->getCode() );
        }

        if( is_wp_error( $controlCode ) ){
            return $controlCode;
        }

        $response = rest_ensure_response([
            'control' => $controlCode
        ]);
        $response->set_status(201);

        return $response;
    }



}