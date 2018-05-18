import React from 'react';
import PropTypes from 'prop-types';
import {FormGroup, Button, ControlLabel, Checkbox, HelpBlock, PanelGroup, Panel} from 'react-bootstrap';
import {FieldsPrivacySettings} from "./FieldsPrivacySettings";
import remove from 'lodash.remove';

/**
 * All privacy settings for a form
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const FormPrivacySettings = (props) => {
    /**
     * Toggle exporter enabled
     *
     * @since 1.7.0
     */
    const toggleEnable = () => {
        props.onStateChange({
            ...props.privacySettings,
            privacyExporterEnabled: !props.privacySettings.privacyExporterEnabled,
        });
    };

    /**
     * Add a field to list if in list, remove if not in list
     *
     * @since 1.7.0
     * @param {Array} fields
     * @param {String} fieldId
     * @returns {*}
     */
    function updateFieldsList(fields, fieldId) {
        if (!fields.length || !fields.includes(fieldId)) {
            fields.push(fieldId)
        } else {
            if( 1 === fields.length ){
                fields = [];
            }else{
                fields = remove(fields, (ID) => {
                    return ID === fieldId;
                });
            }

        }
        return fields;
    }

    /**
     * Toggle a field as an email field to identify users by or not.
     *
     * @since 1.7.0
     *
     * @param {String} fieldId
     */
    const toggleIsEmail = (fieldId) => {
        let emailIdentifyingFields = updateFieldsList(props.privacySettings.emailIdentifyingFields, fieldId);
        props.onStateChange({
            ...props.privacySettings,
            emailIdentifyingFields,
        });
    };

    /**
     * Toggle a field as a PII field or not
     *
     * @since 1.7.0
     *
     * @param fieldId
     */
    const toggleIsPii = (fieldId) => {
        let piiFields = updateFieldsList(props.privacySettings.piiFields, fieldId);
        props.onStateChange({
            ...props.privacySettings,
            piiFields,
        });
    };

    return (
        <section
            className={'layout-grid'}
        >
            <h2>{props.form.name}</h2>

            <div
                className={'row'}
            >
                <div
                    className={'col-xs-8'}
                >
                    <FormGroup controlId={`caldera-forms-privacy-gdpr-enable-${props.form.ID}`}>
                        <ControlLabel>
                            Enable GDPR Exporter For Form
                        </ControlLabel>
                        <Checkbox
                            onChange={toggleEnable}
                            checked={props.privacySettings.privacyExporterEnabled}
                        >
                            Enable
                        </Checkbox>
                        <HelpBlock>If checked data for this form will be added to GDPR personal data requests, and
                            deletes.</HelpBlock>}
                    </FormGroup>

                    {props.privacySettings.privacyExporterEnabled &&
                        <section>
                            <h3>Field Settings</h3>
                            <FieldsPrivacySettings
                                fields={props.form.fields}
                                formId={props.form.ID}
                                privacySettings={props.privacySettings}
                                onCheckIsEmail={toggleIsEmail}
                                onCheckIsPii={toggleIsPii}
                            />
                        </section>
                    }
                </div>
                <div
                    style={{
                        backgroundColor: '#fff'
                    }}
                    className={'col-xs-4'}
                >
                    <p 
                        style={{
                            margin: 0,
                            padding: '.7em 1em',
                            borderBottom: '1px solid #eee'
                        }}
                    >
                        <Button
                            type="submit"
                            onClick={() => {
                                props.onSave(props.privacySettings, props.form.ID)
                            }}
                            className={'primary button button-primary'}
                        >
                            Save
                        </Button>
                    </p>
                    <ul
                        style={{padding:'12px'}}
                    >
                        <li>Email Identifying Fields: The field(s) of your form that can be used to determine whose data an entry belongs to.</li>
                        <li>Personally Identifying Fields (fields): The field(s) of your form that contain PII about the person identified in the email identifying field.</li>
                        <li><a href={'https://calderaforms.com/gdpr?utm_campaign=wp-admin'}>Learn More Here</a></li>
                    </ul>
                </div>
            </div>




        </section>
    );


};

FormPrivacySettings.propTypes = {
    form: PropTypes.object.isRequired,
    onSave: PropTypes.func.isRequired,
    privacySettings: PropTypes.shape({
        emailIdentifyingFields: PropTypes.array,
        piiFields: PropTypes.array,
        privacyExporterEnabled: PropTypes.bool,
        fields: PropTypes.object,
    }).isRequired,
    onStateChange: PropTypes.func.isRequired,
};