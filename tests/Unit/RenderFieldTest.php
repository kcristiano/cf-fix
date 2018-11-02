<?php

namespace calderawp\calderaforms\Tests\Unit;

use calderawp\calderaforms\cf2\Fields\RenderField;
use Brain\Monkey;
use calderawp\calderaforms\Tests\Util\Traits\HasFileFieldConfigs;

class RenderFieldTest extends TestCase
{
    use HasFileFieldConfigs;
    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::__construct()
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::$field
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::$formIdAttr
     */
    public function test__construct()
    {
        $field = $this->fieldFactory('email' );
        $formIdAttr = 'cf1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertAttributeEquals($formIdAttr, 'formIdAttr', $renderer );
        $this->assertAttributeEquals($field, 'field', $renderer );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getFieldIdAttr();
     */
    public function testGetFieldIdAttr()
    {
        $field = $this->fieldForRenderFactory();
        $formIdAttr = 'cf1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertEquals($field['fieldIdAttr'], $renderer->getFieldIdAttr() );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getFormIdAttr();
     */
    public function testGetFormIdAttr()
    {
        $field = $this->fieldForRenderFactory();
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertEquals($formIdAttr, $renderer->getFormIdAttr() );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::data();
     */
    public function testData()
    {
        $fieldId = 'fld_1';
        $field = $this->fieldForRenderFactory($fieldId);
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $data = $renderer->data();
        $this->assertEquals([
            'type' => 'text',
            'outterIdAttr' => 'cf2-fld_1',
            'fieldId' => 'fld_1',
            'fieldLabel' => 'Email',
            'fieldCaption' => 'Make emails',
            'fieldPlaceHolder' => '',
            'required' => 1,
            'fieldDefault' => '',
            'fieldValue' => '',
            'fieldIdAttr' => 'fld_1',
            'configOptions' => []
        ],$data);
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::data();
     */
    public function testDataFileFieldOptions()
    {
        $a = [];
        foreach (array_keys($this->getFileFieldConfigs() ) as $fieldId){
            $field = $this->fieldForRenderFactory($fieldId);
            $formIdAttr = 'cf1_1';
            $renderer = new RenderField($formIdAttr,$field );
            $data = $renderer->data();
            $fieldConfig = $this->getFileFieldConfig($fieldId)['config'];
            $configOptions = $data['configOptions' ];
            $a[$field['ID']]=$data;
            $this->assertArrayHasKey('multiple',$configOptions);
            $this->assertArrayHasKey('multiUploadText',$configOptions);
            $this->assertArrayHasKey('allowedTypes',$configOptions);

            if (isset($fieldConfig['multi_upload'])) {
                $this->assertEquals($fieldConfig['multi_upload'], $configOptions['multiple']);
            }
            if (isset($fieldConfig['multi_upload_text'])) {
                $this->assertEquals($fieldConfig['multi_upload_text'], $configOptions['multiUploadText']);
            }

        }


    }

	/**
	 * @covers \calderawp\calderaforms\cf2\Fields\RenderField::data();
	 */
	public function testDataFileFieldIncludesAllowedType()
	{


		$fieldId = 'allows_png_only';
		$field = $this->fieldForRenderFactory($fieldId);
		$field[ 'type' ] = 'cf2_file';
		$formIdAttr = 'cf1_1';
		$renderer = new RenderField($formIdAttr,$field );
		$data = $renderer->data();
		$allowed = $data['configOptions' ]['allowedTypes'];
		$this->assertSame('.png', $allowed );


	}


	/**
	 * @covers \calderawp\calderaforms\cf2\Fields\RenderField::data();
	 */
	public function testDataFileFieldIncludesAllowedTypes()
	{


		$fieldId = 'allows_jpg_and_png';
		$field = $this->fieldForRenderFactory($fieldId);
		$field[ 'type' ] = 'cf2_file';
		$formIdAttr = 'cf1_1';
		$renderer = new RenderField($formIdAttr,$field );
		$data = $renderer->data();
		$allowed = $data['configOptions' ]['allowedTypes'];
		$this->assertSame('.png,.jpg', $allowed );

	}



	/**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getType();
     */
    public function testGetType()
    {
        $fieldId = 'fld_1';
        $field = $this->fieldForRenderFactory($fieldId);
        $field[ 'type' ] = 'cf2_file';
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $data = $renderer->data();
        $this->assertEquals('file',$data['type']);
    }





    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::render();
     */
    public function testRender()
    {

        $field = $this->fieldForRenderFactory();
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $markup = $renderer->render();
        $this->assertNotFalse(
            strpos( $markup,'class="cf2-field-wrapper"')
        );
        $this->assertNotFalse(
            strpos( $markup,$renderer->getOuterIdAttr() )
        );
        $this->assertNotFalse(
            strpos( $markup,'data-field-id=')
        );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getOuterIdAttr();
     */
    public function testGetOuterIdAttr()
    {
        $fieldId = 'fld_1';
        $field = $this->fieldForRenderFactory($fieldId);
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertEquals("cf2-$fieldId", $renderer->getOuterIdAttr() );
    }




    /**
     * @return array
     */
    protected function fieldForRenderFactory($fieldId = null )
    {
        if( empty($field = $this->getFileFieldConfig($fieldId))){
            $field = $this->fieldFactory('email', $fieldId);
        }else{
            $field[ 'type' ] = 'cf2_file';
        }
        $field = array_merge($field, ['fieldIdAttr' => $field['ID'] ]);
        return $field;
    }
}
