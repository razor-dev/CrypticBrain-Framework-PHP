<?php
/**
 * CFormValidation widget helper class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * init
 *
 */

class CFormValidation
{
    const NL = "\n";

    /**
     * Performs form validation
     * @param array $params
     * @return mixed
     *
     * Usage: (in Controller class)
     * - possible validation types:
     *  	alpha, numeric, alphanumeric, variable, mixed, phone, phoneString, username, timeZone
     *  	password, email, fileName, date, integer, positiveInteger, float, any, confirm,
     *  	url, range ('minValue'=>'' and 'maxValue'=>''), set, text
     * - attribute 'validation'=>array(..., 'forbiddenChars'=>array('+', '$')) is used to define forbidden characters
     * - attribute 'validation'=>array(..., 'trim'=>true) - removes spaces from field value before validation
     *
     * $result = CWidget::create('CFormValidation', array(
     *     'fields'=>array(
     *         'field_1'=>array('title'=>'Username',        'validation'=>array('required'=>true, 'type'=>'username')),
     *         'field_2'=>array('title'=>'Password',        'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6)),
     *         'field_3'=>array('title'=>'Repeat Password', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_2')),
     *         'field_4'=>array('title'=>'Email',           'validation'=>array('required'=>true, 'type'=>'email')),
     *         'field_5'=>array('title'=>'Confirm Email',   'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_4')),
     *         'field_6'=>array('title'=>'Mixed',           'validation'=>array('required'=>true, 'type'=>'mixed')),
     *         'field_7'=>array('title'=>'Field',           'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255)),
     *         'field_8'=>array('title'=>'Image',           'validation'=>array('required'=>true, 'type'=>'image', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'maxWidth'=>'120px', 'maxHeight'=>'90px', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'')),
     *         'field_9'=>array('title'=>'File',            'validation'=>array('required'=>true, 'type'=>'file', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'mimeType'=>'application/zip, application/xml', 'fileName'=>'')),
     *        'field_10'=>array('title'=>'Price',           'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'', 'maxValue'=>'', 'format'=>'american|european'),
     *        'field_11'=>array('title'=>'Format',          'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(1, 2, 3, 4, 5))),
     *     ),
     *     'messagesSource'=>'core',
     *     'showAllErrors'=>false,
     * ));
     *
     * if($result['error']){
     *     $msg = $result['errorMessage'];
     *     $this->_view->errorField = $result['errorField'];
     *     $msgType = 'validation';
     * }else{
     *     // your code here to handle a successful submission...
     * }
     */
    public static function init($params = array())
    {
        /** @var CHttpRequest $request */
        $request = CrypticBrain::app()->getRequest();
        $output = array('error'=>false, 'uploadedFiles'=>array());
        $fields = isset($params['fields']) ? $params['fields'] : array();
        $showAllErrors = isset($params['showAllErrors']) ? (bool)$params['showAllErrors'] : false;

        foreach($fields as $field => $fieldInfo){
            $title          = isset($fieldInfo['title']) ? $fieldInfo['title'] : $fieldInfo['placeholder'] | '';
            $required       = isset($fieldInfo['validation']['required']) ? $fieldInfo['validation']['required'] : false;
            $type           = isset($fieldInfo['validation']['type']) ? $fieldInfo['validation']['type'] : 'any';
            $forbiddenChars = isset($fieldInfo['validation']['forbiddenChars']) ? $fieldInfo['validation']['forbiddenChars'] : array();
            $minLength      = isset($fieldInfo['validation']['minLength']) ? $fieldInfo['validation']['minLength'] : '';
            $maxLength      = isset($fieldInfo['validation']['maxLength']) ? (int)$fieldInfo['validation']['maxLength'] : '';
            $minValue       = isset($fieldInfo['validation']['minValue']) ? $fieldInfo['validation']['minValue'] : '';
            $maxValue       = isset($fieldInfo['validation']['maxValue']) ? $fieldInfo['validation']['maxValue'] : '';
            $maxSize        = isset($fieldInfo['validation']['maxSize']) ? CHtml::convertFileSize($fieldInfo['validation']['maxSize']) : '';
            $minWidth       = isset($fieldInfo['validation']['minWidth']) ? CHtml::convertImageDimensions($fieldInfo['validation']['minWidth']) : '';
            $minHeight      = isset($fieldInfo['validation']['minHeight']) ? CHtml::convertImageDimensions($fieldInfo['validation']['minHeight']) : '';
            $maxWidth       = isset($fieldInfo['validation']['maxWidth']) ? CHtml::convertImageDimensions($fieldInfo['validation']['maxWidth']) : '';
            $maxHeight      = isset($fieldInfo['validation']['maxHeight']) ? CHtml::convertImageDimensions($fieldInfo['validation']['maxHeight']) : '';
            $fileMimeType   = isset($fieldInfo['validation']['mimeType']) ? $fieldInfo['validation']['mimeType'] : '';
            $targetPath     = isset($fieldInfo['validation']['targetPath']) ? $fieldInfo['validation']['targetPath'] : '';
            $fileMimeTypes  = (!empty($fileMimeType)) ? explode(',', str_replace(' ', '', $fileMimeType)) : array();
            $fileDefinedName = isset($fieldInfo['validation']['fileName']) ? $fieldInfo['validation']['fileName'] : '';
            $trim           = isset($fieldInfo['validation']['trim']) ? (bool)$fieldInfo['validation']['trim'] : false;
            $fieldValue     = ($trim) ? trim($request->getPost($field)) : $request->getPost($field);
            $errorMessage   = '';
            $valid = true;

            if($type == 'file' || $type == 'image'){
                $fileName     = (isset($_FILES[$field]['name'])) ? $_FILES[$field]['name'] : '';
                $fileSize     = (isset($_FILES[$field]['size'])) ? $_FILES[$field]['size'] : 0;
                $fileTempName = (isset($_FILES[$field]['tmp_name'])) ? $_FILES[$field]['tmp_name'] : '';
                $fileError    = (isset($_FILES[$field]['error'])) ? $_FILES[$field]['error'] : '';
                $fileType     = (isset($_FILES[$field]['type'])) ? $_FILES[$field]['type'] : '';
                $fileWidth    = '';
                $fileHeight   = '';
                if($type == 'image'){
                    if($required && !isset($_FILES[$field]['tmp_name'])){
                        $required = false;
                    }else{
                        if(function_exists('image_type_to_mime_type') && function_exists('exif_imagetype')){
                            $fileType = image_type_to_mime_type(exif_imagetype($fileTempName));
                        }else{
                            $image = getimagesize($_FILES[$field]['tmp_name']);
                            $fileType = $image['mime'];
                        }
                        $fileWidth = CImage::getImageSize($fileTempName, 'width');
                        $fileHeight = CImage::getImageSize($fileTempName, 'height');
                    }
                }

                if($required && empty($fileSize)){
                    $valid = false;
                    $errorMessage = CrypticBrain::t('core', 'The field {title} cannot be empty!', array('{title}'=>$title));
                }elseif(!empty($fileSize)){
                    if($maxSize !== '' && $fileSize > $maxSize){
                        $valid = false;
                        $sFileSize = number_format(($fileSize / 1024), 2, '.', ',').' Kb';
                        $sMaxAllowed = number_format(($maxSize / 1024), 2, '.', ',').' Kb';
                        $errorMessage = CrypticBrain::t('core', 'Invalid file size for field {title}: {file_size} (max. allowed: {max_allowed})', array('{title}'=>$title, '{file_size}'=>$sFileSize, '{max_allowed}'=>$sMaxAllowed));
                    }elseif(!empty($fileMimeTypes) && !in_array($fileType, $fileMimeTypes)){
                        $valid = false;
                        $errorMessage = CrypticBrain::t('core', 'Invalid file type for field {title}: you may only upload {mime_type} files.', array('{title}'=>$title, '{mime_type}'=>$fileMimeType));
                    }elseif($minWidth !== '' && $fileWidth < $minWidth){
                        $valid = false;
                        $errorMessage = CrypticBrain::t('core', 'Invalid image width for field {title}: {image_width}px (min. allowed: {min_allowed}px)', array('{title}'=>$title, '{image_width}'=>$fileWidth, '{min_allowed}'=>$minWidth));
                    }elseif($minHeight !== '' && $fileHeight < $minHeight){
                        $valid = false;
                        $errorMessage = CrypticBrain::t('core', 'Invalid image height for field {title}: {image_height}px (min. allowed: {min_allowed}px)', array('{title}'=>$title, '{image_height}'=>$fileHeight, '{min_allowed}'=>$minHeight));
                    }elseif($maxWidth !== '' && $fileWidth > $maxWidth){
                        $valid = false;
                        $errorMessage = CrypticBrain::t('core', 'Invalid image width for field {title}: {image_width}px (max. allowed: {max_allowed}px)', array('{title}'=>$title, '{image_width}'=>$fileWidth, '{max_allowed}'=>$maxWidth));
                    }elseif($maxHeight !== '' && $fileHeight > $maxHeight){
                        $valid = false;
                        $errorMessage = CrypticBrain::t('core', 'Invalid image height for field {title}: {image_height}px (max. allowed: {max_allowed}px)', array('{title}'=>$title, '{image_height}'=>$fileHeight, '{max_allowed}'=>$maxHeight));
                    }else{
                        $targetFileName = (!empty($fileDefinedName)) ? $fileDefinedName.'.'.pathinfo($fileName, PATHINFO_EXTENSION) : basename($fileName);
                        $targetFullName = $targetPath.$targetFileName;
                        if(APP_MODE == 'demo'){
                            $valid = false;
                            $errorMessage = CrypticBrain::t('core', 'This operation is blocked in Demo Mode!');
                        }elseif(@move_uploaded_file($fileTempName, $targetFullName)){
                            $output['uploadedFiles'][] = $targetFullName;
                        }else{
                            $valid = false;
                            $errorMessage = CrypticBrain::t('core', 'An error occurred while uploading your file for field {title}. Please try again.', array('{title}'=>$title));
                        }
                    }
                }
            }elseif($required && trim($fieldValue) == ''){
                $valid = false;
                $errorMessage = CrypticBrain::t('core', 'The field {title} cannot be empty!', array('{title}'=>$title));
            }elseif($type == 'confirm'){
                $confirmField = isset($fieldInfo['validation']['confirmField']) ? $fieldInfo['validation']['confirmField'] : '';
                $confirmFieldValue = $request->getPost($confirmField);
                $confirmFieldName = isset($fields[$confirmField]['title']) ? $fields[$confirmField]['title'] : '';
                if($confirmFieldValue != $fieldValue){
                    $valid = false;
                    $errorMessage = CrypticBrain::t('core', 'The {confirm_field} and {title} fields do not match!', array('{confirm_field}'=>$confirmFieldName, '{title}'=>$title));
                }
            }elseif($fieldValue !== ''){
                if(!empty($minLength) && !CValidator::validateMinlength($fieldValue, $minLength)){
                    $valid = false;
                    $errorMessage = CrypticBrain::t('core', 'The {title} field length must be at least {min_length} characters!', array('{title}'=>$title, '{min_length}'=>$minLength));
                }elseif(!empty($maxLength) && !CValidator::validateMaxlength($fieldValue, $maxLength)){
                    $valid = false;
                    $errorMessage = CrypticBrain::t('core', 'The {title} field length may be {max_length} characters maximum!', array('{title}'=>$title, '{max_length}'=>$maxLength));
                }elseif(is_array($forbiddenChars) && !empty($forbiddenChars)){
                    foreach($forbiddenChars as $char){
                        if(preg_match('/'.$char.'/i', $fieldValue)){
                            $valid = false;
                            $errorMessage = CrypticBrain::t('core', 'The {title} field contains one or more forbidden characters from this list: {characters} !', array('{title}'=>$title, '{characters}'=>implode(' ', $forbiddenChars)));
                            break;
                        }
                    }
                }

                if($valid){
                    switch($type){
                        case 'alpha':
                            $valid = CValidator::isAlpha($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid alphabetic value!', array('{title}'=>$title));
                            break;
                        case 'numeric':
                            $valid = CValidator::isNumeric($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid numeric value!', array('{title}'=>$title));
                            break;
                        case 'alphanumeric':
                            $valid = CValidator::isAlphaNumeric($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid alpha-numeric value!', array('{title}'=>$title));
                            break;
                        case 'mixed':
                            $valid = CValidator::isMixed($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} should include only alpha, space and numeric characters!', array('{title}'=>$title));
                            break;
                        case 'phone':
                            $valid = CValidator::isPhone($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid phone number!', array('{title}'=>$title));
                            break;
                        case 'username':
                            $valid = CValidator::isUsername($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must have a valid username value!', array('{title}'=>$title));
                            break;
                        case 'email':
                            $valid = CValidator::isEmail($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid email address!', array('{title}'=>$title));
                            break;
                        case 'fileName':
                            $valid = CValidator::isFileName($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid file name!', array('{title}'=>$title));
                            break;
                        case 'date':
                            $valid = CValidator::isDate($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid date value!', array('{title}'=>$title));
                            break;
                        case 'integer':
                            $valid = CValidator::isInteger($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid integer value!', array('{title}'=>$title));
                            break;
                        case 'set':
                            $setArray  = isset($fieldInfo['validation']['source']) ? $fieldInfo['validation']['source'] : array();
                            $valid = CValidator::inArray($fieldValue, $setArray);
                            $errorMessage = CrypticBrain::t('core', 'You must agree with the field {title}!', array('{title}'=>$title));
                            break;
                        case 'range':
                            if($minValue == '') $minValue = '?';
                            if($maxValue == '') $maxValue = '?';
                            $valid = CValidator::validateRange($fieldValue, $minValue, $maxValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be between {min} and {max}!', array('{title}'=>$title, '{min}'=>$minValue, '{max}'=>$maxValue));
                            break;
                        case 'text':
                            $valid = CValidator::isText($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid textual value!', array('{title}'=>$title));
                            break;
                        case 'url':
                            $valid = CValidator::isUrl($fieldValue);
                            $errorMessage = CrypticBrain::t('core', 'The field {title} must be a valid URL string value!', array('{title}'=>$title));
                            break;
                        case 'captcha':
                            $valueFromSession = strtolower(CrypticBrain::app()->getSession()->get('captcha'));
                            if($valueFromSession != strtolower($fieldValue)){
                                $valid = false;
                                $errorMessage = CrypticBrain::t('core', 'The code you entered and code from picture do not match!');
                            }
                            break;
                        case 'any':
                        default:
                            break;
                    }
                }
            }

            if(!$valid){
                $output['error'] = true;
                if($showAllErrors){
                    if($output['errorField'] == '') $output['errorField'] = $field;
                    $output['errorMessage'] .= $errorMessage.'<br />';
                }else{
                    $output['errorField'] = $field;
                    $output['errorMessage'] = $errorMessage;
                    break;
                }
            }
        }
        return $output;
    }
}