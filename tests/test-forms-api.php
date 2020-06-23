<?php



class MockFormAPI extends Caldera_Forms_Forms {

	public static function get_stored_forms()
	{
		return parent::get_stored_forms(); // TODO: Change the autogenerated stub
	}
}

class Test_Caldera_Forms_API extends Caldera_Forms_Test_Case
{

	public function setUp()
	{
		$forms = Caldera_Forms_Forms::get_forms(FALSE, TRUE);
		if ( !empty($forms) ) {
			$ids = [];
			foreach ( $forms as $form ) {
				$ids[] = $form;
			}
			if ( !empty($ids) ) {
				Caldera_Forms_DB_Form::get_instance()->delete($ids);
			}
		}
		parent::setUp(); // TODO: Change the autogenerated stub
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers Caldera_Forms_Forms::get_forms()
	 * @covers Caldera_Forms_Forms::get_stored_forms()
	 */
	public function testGetStoredForms()
	{
		$forms = Caldera_Forms_Forms::get_forms(FALSE, TRUE);
		$this->assertEmpty($forms);
		$this->assertTrue(is_array($forms));
	}


	/**
	 * Compare the database abstraction to the forms API
	 *
	 * @since 1.7.0
	 *
	 * @covers Caldera_Forms_Forms::get_forms()
	 * @covers Caldera_Forms_Forms::get_stored_forms()
	 */
	public function testGetFormVsDbForm()
	{
		$form_one_id = $this->import_autoresponder_form();
		$form_two_id = $this->import_contact_form();
		$db_results = Caldera_Forms_DB_Form::get_instance()->get_all(TRUE);
		$db_ids = wp_list_pluck($db_results, 'form_id');
		$this->assertCount(2, $db_ids);
		$this->assertCount(2, Caldera_Forms_Forms::get_forms(FALSE, TRUE));
		$this->assertTrue(in_array($form_one_id, $db_ids));
		$this->assertTrue(in_array($form_two_id, $db_ids));
		$this->assertSame(array_values($db_ids), array_values(Caldera_Forms_Forms::get_forms(FALSE, TRUE)));
	}

	/**
	 * Make sure forms added on the caldera_forms_get_forms filter work
	 *
	 * @since 1.7.0
	 * @covers caldera_forms_get_forms filter
	 * @covers Caldera_Forms_Forms::get_forms()
	 *
	 */
	public function testFilterAddedForms()
	{

		//No forms because second argument is true, ignoring forms added on filter is expected.
		$this->assertCount(0, Caldera_Forms_Forms::get_forms(FALSE, TRUE));
		//Same number of forms if $with_details is true, was false in previous assertion
		$this->assertCount(0, Caldera_Forms_Forms::get_forms(true,true));

		//Three forms added on filter caldera_forms_get_forms in bootstrap.php
		$this->assertCount(3, Caldera_Forms_Forms::get_forms(FALSE));
		//Same number of forms if $with_details is true, was false in previous assertion
		$this->assertCount(3, Caldera_Forms_Forms::get_forms(true,false));

		//add one more form and check again
		$this->import_contact_form();
		$this->assertCount(4, Caldera_Forms_Forms::get_forms(FALSE));
		//Same number of forms if $with_details is true, was false in previous assertion
		$this->assertCount(4, Caldera_Forms_Forms::get_forms(true ));


	}


	/**
	 * Test forms list without details
	 *
	 * @since 1.7.0
	 *
	 * @covers Caldera_Forms_Forms::get_forms()
	 * @covers Caldera_Forms_Forms::get_stored_forms()
	 */
	public function testGetFormsNoDetails()
	{
		$forms = Caldera_Forms_Forms::get_forms(TRUE, TRUE);
		$this->assertEmpty($forms);
		$this->assertTrue(is_array($forms));

		$form_one_id = $this->import_autoresponder_form();
		$form_two_id = $this->import_contact_form();
		$forms = Caldera_Forms_Forms::get_forms(FALSE, TRUE);
		$this->assertCount(2, $forms);
		$this->assertArrayHasKey($form_one_id, $forms);
		$this->assertArrayHasKey($form_two_id, $forms);
		$this->assertEquals([ $form_one_id, $form_two_id ], array_keys($forms));
		$this->assertEquals([ $form_one_id, $form_two_id ], array_values($forms));
		$form_three_id = $this->import_contact_form(FALSE);
		$forms = Caldera_Forms_Forms::get_forms(FALSE, TRUE);
		$this->assertCount(3, $forms);
		$this->assertArrayHasKey($form_one_id, $forms);
		$this->assertArrayHasKey($form_two_id, $forms);
		$this->assertArrayHasKey($form_three_id, $forms);
		$this->assertEquals([ $form_one_id, $form_two_id, $form_three_id ], array_keys($forms));
		$this->assertEquals([ $form_one_id, $form_two_id, $form_three_id ], array_values($forms));

    }

    /**
     * Test forms list with details
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Forms::get_forms()
     * @covers Caldera_Forms_Forms::get_stored_forms()
     */
    public function testGetFormsWithDetails()
    {

		//https://github.com/CalderaWP/Caldera-Forms/issues/2736#issuecomment-436678659
		$forms = Caldera_Forms_Forms::get_forms(TRUE, TRUE);
		$this->assertEmpty($forms);
		$this->assertTrue(is_array($forms));

		$form_one_id = $this->import_autoresponder_form();
		$form_two_id = $this->import_contact_form();
		$forms = Caldera_Forms_Forms::get_forms(TRUE, TRUE);
		$this->assertCount(2, $forms);

		$this->assertArrayHasKey($form_one_id, $forms);
		$this->assertArrayHasKey($form_two_id, $forms);

		$form = Caldera_Forms_Forms::get_form($form_one_id);
		$this->assertSame($forms[ $form_one_id ][ 'name' ], $form[ 'name' ]);
		$this->assertSame($forms[ $form_one_id ][ 'ID' ], $form[ 'ID' ]);
		$this->assertSame($forms[ $form_one_id ][ 'pinned' ], $form[ 'pinned' ]);
		$this->assertSame($forms[ $form_one_id ][ 'db_support' ], $form[ 'db_support' ]);

		$form = Caldera_Forms_Forms::get_form($form_two_id);
		$this->assertSame($forms[ $form_two_id ][ 'name' ], $form[ 'name' ]);
		$this->assertSame($forms[ $form_two_id ][ 'ID' ], $form[ 'ID' ]);
		$this->assertSame($forms[ $form_two_id ][ 'pinned' ], $form[ 'pinned' ]);
		$this->assertSame($forms[ $form_two_id ][ 'db_support' ], $form[ 'db_support' ]);
	}

    /**
     * @group now
     */
	public function testGetFormsUsesCache(){
        global $wpdb;
        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();
        $forms = Caldera_Forms_Forms::get_forms(TRUE, TRUE);
        $this->assertCount(2, $forms);
        $this->assertFalse(is_null($wpdb->last_query));

        $wpdb->last_query = null;
        $forms = Caldera_Forms_Forms::get_forms(TRUE, TRUE);
        $this->assertCount(2, $forms);
        $this->assertTrue(is_null($wpdb->last_query));

    }

    /**
     * @group now
     */
    public function testGetFormUsesCache(){
        global $wpdb;
        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();
        $forms = Caldera_Forms_Forms::get_forms(TRUE, TRUE);
        $this->assertCount(2, $forms);
        $this->assertFalse(is_null($wpdb->last_query));

        $wpdb->last_query = null;
        $form = Caldera_Forms_Forms::get_form($form_one_id);
        $this->assertTrue(is_null($wpdb->last_query));
        $this->assertSame($forms[ $form_one_id ][ 'name' ], $form[ 'name' ]);
        $this->assertSame($forms[ $form_one_id ][ 'ID' ], $form[ 'ID' ]);

        $form = Caldera_Forms_Forms::get_form($form_two_id);
        $this->assertTrue(is_null($wpdb->last_query));
        $this->assertSame($forms[ $form_two_id ][ 'name' ], $form[ 'name' ]);
        $this->assertSame($forms[ $form_two_id ][ 'ID' ], $form[ 'ID' ]);
    }

	/**
	 * Test created form comes back out of database correctly
	 *
	 * @since 1.7.0
	 *
	 * @covers Caldera_Forms_Forms::create_form()
	 * @covers Caldera_Forms_Forms::get_form()
	 */
	public function testCreateAndGetForm()
	{
		$config = file_get_contents($this->get_path_for_main_mailer_form_import());
		$config = $this->recursive_cast_array(json_decode($config));

		$form = Caldera_Forms_Forms::create_form($config);
		$this->assertTrue(is_array($form));
		$this->assertArrayHasKey('name', $form);
		$this->assertArrayHasKey('ID', $form);
		$this->assertArrayHasKey('mailer', $form);
		$this->assertArrayHasKey('pinned', $form);
		$this->assertArrayHasKey('fields', $form);
		$this->assertArrayHasKey('conditional_groups', $form);
		$this->assertArrayHasKey('version', $form);
		$this->assertArrayHasKey('layout_grid', $form);
		$this->assertArrayHasKey('settings', $form);
		$this->assertSame($config[ 'name' ], $form[ 'name' ]);
		$this->assertSame($config[ 'ID' ], $form[ 'ID' ]);
		$this->assertSame($config[ 'pinned' ], $form[ 'pinned' ]);
		$this->assertSame($config[ 'db_support' ], $form[ 'db_support' ]);
		$this->assertSame($config[ 'fields' ], $form[ 'fields' ]);
		$this->assertSame($config[ 'layout_grid' ], $form[ 'layout_grid' ]);
		$this->assertSame($config[ 'conditional_groups' ], $form[ 'conditional_groups' ]);
		$this->assertSame($config[ 'settings' ], $form[ 'settings' ]);
		$this->assertSame($config[ 'version' ], $form[ 'version' ]);
	}

	/**
	 * Test update of form
	 *
	 * @since 1.7.0
	 *
	 * @covers Caldera_Forms_Forms::get_form()
	 * @covers Caldera_Forms_Forms::save_form()
	 */
	public function testUpdateAndGet()
	{
		$form_id = $this->import_contact_form();
		$form = Caldera_Forms_Forms::get_form($form_id);
		foreach ( $form[ 'fields' ] as $field_id => $field ) {
			$form[ 'fields' ][ $field_id ][ 'type' ] = 'hidden';
		}
		Caldera_Forms_Forms::save_form($form);
		$form = Caldera_Forms_Forms::get_form($form_id);
		$looped = 0;
		foreach ( $form[ 'fields' ] as $field_id => $field ) {
			$looped++;
			$this->assertSame('hidden', Caldera_Forms_Field_Util::get_type($field_id, $form));
		}
		$this->assertSame($looped, count($form[ 'fields' ]));
	}

	/**
	 * Test manual Formulas trimming during forms saving
	 *
	 * @since 1.8.6
	 *
	 * @covers Caldera_Forms_Forms::save_form()
	 */
	public function testManualFormulasTrimDuringSave ()
	{
		$form_id = $this->import_contact_form();
		$form = Caldera_Forms_Forms::get_form($form_id);

		$form['fields']['fld_60011111'] = [
      		'ID' 			=> 'fld_60011111',
      		'type' 			=>	'calculation',
      		'label' 		=>	'Calculation',
      		'slug' 			=>	'calculation',
      		'conditions' 	=>	[],
      		'required' 		=>	1,
			'config'		=>	[
				'manual_formula'	=> '2 + 2\n'
			]
		];
		Caldera_Forms_Forms::save_form($form);

		$form = Caldera_Forms_Forms::get_form($form_id);

		$this->assertSame( '2 + 2', $form['fields']['fld_60011111']['config']['manual_formula'] );
	}

	/**
	 * Test manual Formulas trimming during forms saving
	 *
	 * @since 1.8.6
	 *
	 * @covers Caldera_Forms_Forms::save_form()
	 */
	public function testManualFormulasTrimDuringSaveWithRound ()
	{
		$form_id = $this->import_contact_form();
		$form = Caldera_Forms_Forms::get_form($form_id);

		$form['fields']['fld_60011111'] = [
      		'ID' 			=> 'fld_60011111',
      		'type' 			=>	'calculation',
      		'label' 		=>	'Calculation',
      		'slug' 			=>	'calculation',
      		'conditions' 	=>	[],
      		'required' 		=>	1,
			'config'		=>	[
				'manual_formula'	=> 'round(2 * 2.3333)\r\n'
			]
		];
		Caldera_Forms_Forms::save_form($form);

		$form = Caldera_Forms_Forms::get_form($form_id);

		$this->assertSame( 'round(2 * 2.3333)', $form['fields']['fld_60011111']['config']['manual_formula'] );
	}

    /**
     * Is cache cleared after creating a form?
     *
     * @see https://github.com/CalderaWP/Caldera-Forms/issues/3455
     *
     * @covers Caldera_Forms_Forms::create_form()
     * @covers Caldera_Forms_Forms::get_form()
     * @covers Caldera_Forms_Forms::get_forms()
     */
	public function testCreateGetForms()
    {
        $form = \Caldera_Forms_Forms::create_form([
            'name' => 'One'
        ]);
        $form2 = \Caldera_Forms_Forms::create_form([
            'name' => 'Two'
        ]);
        //Query form all forms, check name is returned correctly for both forms
        $forms = \Caldera_Forms_Forms::get_forms(true,true);
        $this->assertSame($form['name'], $forms[$form['ID']]['name']);
        $this->assertSame($form2['name'], $forms[$form2['ID']]['name']);

        //Query for one form, check is name returned correctly
        $this->assertSame($form['name'],  \Caldera_Forms_Forms::get_form($form['ID'])['name']);
        $this->assertSame($form2['name'],  \Caldera_Forms_Forms::get_form($form2['ID'])['name']);
    }

}
