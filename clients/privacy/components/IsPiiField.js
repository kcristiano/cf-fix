import React from  'react';
import PropTypes from 'prop-types';
import { FormGroup,FormControl,ControlLabel,HelpBlock,Panel,PanelGroup,Checkbox } from 'react-bootstrap';

function fieldIsPii(field,privacySettings ) {
    return privacySettings.piiFields.length && privacySettings.piiFields.includes(field.ID);
}

export const IsPiiField = (props) => {
    const idAttr  = `caldera-forms-privacy-gdpr-is-pii-field-${props.field.ID}`;
    return (
        <FormGroup
            controlid={idAttr}
        >
            <ControlLabel
               id={idAttr}
            >
               Personally Identifying Field?
            </ControlLabel>
            <Checkbox
                id={idAttr}
                onChange={() => {
                        props.onCheck(props.field.ID)
                    }
                }
                checked={fieldIsPii(props.field,props.privacySettings)}
            >
                Enable
            </Checkbox>
            <HelpBlock>Does field contain personally identifying data?</HelpBlock>}
        </FormGroup>
    )


};

IsPiiField.propTypes = {
    field: PropTypes.object.isRequired,
    privacySettings: PropTypes.object.isRequired,
    onCheck: PropTypes.func.isRequired,
};