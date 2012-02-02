<?php
/**
 * Smarty Form System
 * Create, validate and save form data in a consistent way
 * Works with ActiveRecord system
 */
class SmartyForm
{
	
	/**
	 * Prefix for HTML IDs to prevent conflict
	 */
	const HTML_ID_PREFIX = 'sf_';
	
	/**
	 * Form Action attribute
	 *
	 * @var string
	 */
	public $action;
	
	/**
	 * Is current form fresh, i.e. no data gathered?
	 *
	 * @var bool
	 */
	public $fresh = true;
	
	/**
	 * Whether or not to automatically append error message
	 *
	 * @var bool
	 */
	public $appendErrorMessage = true;
	
	/**
	 * Whether or not to validate fields on client side
	 *
	 * @var array
	 */
	public $validateClientSide = false;
	
	/**
	 * Is current data valid?
	 * The value is defined only after when validate method is called
	 *
	 * @var bool
	 */
	public $valid;
	
	private $id;
	
	protected $elements;
	
	private $activeRecords;
	
	private $recordAssoc;
	
	private $recordFuncs;
	
	const FORMHTML_NO_OPENING = 1;
	
	public function __construct($id = '', $action = '')
	{
		$this->id = $id;
		$this->action = $action;
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function add(SF_Element $obj)
	{
		$obj->form = $this;
		$this->elements[$obj->getID()] = $obj;
	}
	
	/**
	 * Get an element
	 *
	 * @param string $id element ID
	 * @return SF_Element
	 */
	public function get($id)
	{
		return $this->elements[$id];
	}
	
	public function exists($id)
	{
		return isset($this->elements[$id]);
	}
	
	/**
	 * Validate a form
	 *
	 * @return bool
	 */
	public function validate()
	{
		$this->valid = true;
		foreach ($this->elements as $v)
		{
			try
			{
				$v->validate();
			}
			catch(InputException $e)
			{
				$v->triggerError($e);
			}
		}
		return $this->valid;
	}
	
	public function getFormOpeningHTML($params = array())
	{
		$html = "<form id='".self::HTML_ID_PREFIX."{$this->id}' method='post' action='".htmlspecialchars($this->action)."'";
		foreach ($params as $k => $v)
		{
			$html .= (' '.$k.'="'.htmlspecialchars($v).'"');
		}
		$html .= ">";
		foreach ($this->elements as $v)
		{
			if ($v->autoAttach)
			{
				$html .= $v->html();
			}
		}
		return $html;
	}
	
	public function getHTML($ele, $subgroup = null, $attr = array())
	{
		$this->elements[$ele]->setAttribute($attr);
		return $this->elements[$ele]->html();
	}
	
	public function getLabelHTML($ele)
	{
		return '<label for="'.$this->elements[$ele]->getHTMLID() .'">'.$this->elements[$ele]->label.'</label>';
	}
	
	public function getFormHTML($template, $elements = null, $options = 0)
	{
		$defaultTemplates = array(
			'table' => '<tr><td>{label}</td><td>{input}</td></tr>'
		);
		
		if (isset($defaultTemplates[$template]))
		{
			$template = $defaultTemplates[$template];
		}
		
		$html = '';
		
		foreach ($this->elements as $k => $v)
		{
			if (!$elements || in_array($k,$elements))
			{
				$html .= str_replace('{label}',$this->getLabelHTML($k),str_replace('{input}',$this->getHTML($k),$template));
			}
		}
		return $html;
	}
	
	/**
	 * Add ActiveRecord
	 */
	public function addRecord($id, ActiveRecord $record)
	{
		$this->activeRecords[$id] = $record;
	}
	
	/**
	 * Bind an element with a property of ActiveRecord
	 * Validators and Sanitizers are automatically transferred
	 */
	public function bind($elementID, $objID, $property = null)
	{
		$cls = get_class($this->activeRecords[$objID]);
		
		if (!$property)
		{
			$property = $elementID;
		}
		
		// Add validators and sanitizers
		if (isset($cls::$schema[$property]['validator']))
			$this->elements[$elementID]->addValidator($cls::$schema[$property]['validator']);
		if (isset($cls::$schema[$property]['sanitizer']))
			$this->elements[$elementID]->addSanitizer($cls::$schema[$property]['sanitizer']);
		
		// Add Association record
		$this->recordAssoc[$elementID] = array($objID, $property);
	}
	
	/**
	 * Bind activerecord using customized functions
	 *
	 * @param string $objID Object ID
	 * @param callback $gatherer Gatherer object, which takes two parameters: form object and record object
	 * @param callback $saver Saver function, which takes the same two parameters
	 */
	public function bindByFunc($objID, $gatherer, $saver)
	{
		if ($gatherer)
			$this->recordFuncs[$objID]['gatherer'][] = $gatherer;
		if ($saver)
			$this->recordFuncs[$objID]['saver'][] = $saver;
	}
	
	/**
	 * Gather data from HTTP POST request
	 */
	public function gatherFromPOST()
	{
		$this->fresh = false;
		foreach ($this->elements as $v)
		{
			$v->gatherFromPOST();
		}
	}
	
	/**
	 * Gather data from Session
	 */
	public function gatherFromSession()
	{
		$this->fresh = false;
		foreach ($this->elements as $v)
		{
			$v->gatherFromSession();
		}
	}
	
	/**
	 * Gather data from Record
	 */
	public function gatherFromRecord()
	{
		$this->fresh = false;
		foreach ($this->recordFuncs as $objID => $v)
		{
			foreach ($v['gatherer'] as $func)
			{
				$func($this, $this->activeRecords[$objID]);
			}
		}
		foreach ($this->recordAssoc as $k => $v)
		{
			$prop = $v[1];
			if (isset($this->activeRecords[$v[0]]->$prop))
			{
				$this->elements[$k]->data = $this->activeRecords[$v[0]]->$prop;
			}
		}
	}
	
	
	/**
	 * Save to ActiveRecord
	 */
	public function saveToRecord()
	{
		foreach ($this->recordFuncs as $objID => $v)
		{
			foreach ($v['saver'] as $func)
			{
				$func($this, $this->activeRecords[$objID]);
			}
		}
		foreach ($this->recordAssoc as $k => $v)
		{
			$prop = $v[1];
			$this->activeRecords[$v[0]]->$prop = $this->elements[$k]->data;
		}
	}
}

abstract class SF_Element
{
	private $validators;
	private $sanitizers;
	
	protected $attributes = array();
	
	public $autoAttach = false;
	
	/**
	 * @var SmartyForm
	 */
	public $form;
	
	public $data = null;
	
	public $label;
	
	protected $id;
	
	protected $errorMessage;
	
	public function getHTMLID()
	{
		return SmartyForm::HTML_ID_PREFIX . $this->form->getID() . '_' . $this->id;
	}
	
	public function triggerError($e)
	{
		if ($e instanceof Exception)
		{
			$this->errorMessage = $e->getMessage();
		}else
		{
			$this->errorMessage = $e;
		}
		$this->form->valid = false;
	}
	
	public function generateErrorHTML()
	{
		if ($this->errorMessage)
		{
			return "<div class='sf_error_message'>{$this->errorMessage}</div>";
		}else if ($this->form->validateClientSide)
		{
			return "<div class='sf_error_message' style='display: none;'></div>";
		}else
		{
			return '';
		}
	
	}
	
	public function __construct()
	{
		$prop = func_get_args();
		if (func_num_args() > 1)
		{
			for ($i = 0; $i < func_num_args(); $i += 2)
			{
				$k = $prop[$i];
				$v = $prop[$i+1];
				if ($k == 'validator')
				{
					$this->addValidator($v);
				}else{
					$this->$k = $v;
				}
			}
		}
		else
		{
			$this->id = $prop[0];
		}
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function gatherFromPOST()
	{
		$this->data = IO::POST($this->id, null);
	}
	
	public function gatherFromSession()
	{
		$this->data = IO::Session('sf_'.$this->form->getID() . '_' . $this->getID(),null,'unserialize');
	}
	
	/**
	 * Save gathered data to session
	 * Useful for multi-staged form
	 */
	public function saveToSession()
	{
		if ($this->data)
			IO::SetSession('sf_'.$this->form->getID() . '_' . $this->getID(), serialize($this->data));
	}
	
	/**
	 * Add a Validator
	 *
	 * @param callback $v Sanitizer function
	 */
	public function addSanitizer($s)
	{
		$this->sanitizers[] = $s;
	}
	
	/**
	 * Add a Validator
	 *
	 * @param callback $v Validator function
	 */
	public function addValidator($v)
	{
		$this->validators[] = $v;
	}
	
	/**
	 * Validate
	 *
	 * @throws InputException
	 */
	public function validate()
	{
		if (is_array($this->validators))
		{
			foreach ($this->validators as $v)
			{
				$v($this->data);
			}
		}
	}
	
	/**
	 * Sanitize
	 */
	public function sanitize()
	{
		foreach ($this->sanitizers as $v)
		{
			$v($this->data);
		}
	}
	
	public function setAttribute($key, $value = null)
	{
		if (is_array($key))
		{
			foreach ($key as $k=>$v)
			{
				$this->attributes[$k] = $v;
			}
		} else
		{
			if ($value === null)
			{
				unset($this->attributes[$key]);
			}else
			{
				$this->attributes[$key] = $value;
			}
		}
	}
	
	protected function generateAtrributes()
	{
		$attr = '';
		foreach ($this->attributes as $k => $v)
		{
			$attr .= (' '.$k.'="'.htmlspecialchars($v).'"');
		}
		return $attr;
	}
	
	protected function setDefaultAttributes()
	{
		$this->attributes['id'] = $this->getHTMLID();
		$this->attributes['name'] = $this->id;
		if ($this->data)
			$this->attributes['value'] = $this->data;
	}
	
	abstract function html();
	
	protected function appendedErrorMessage()
	{
		return ($this->form->appendErrorMessage && $this->errorMessage) ? $this->generateErrorHTML() : '';
	}
}

abstract class SF_GroupElement extends SF_Element
{
	
}

class SF_Hidden extends SF_TextBased
{
	public $autoAttach = true;
	protected function setDefaultAttributes()
	{
		parent::setDefaultAttributes();
		$this->attributes['type'] = 'hidden';
	}
}

class SF_TextArea extends SF_Element
{
	public function html()
	{
		return '<textarea id="'.$this->getHTMLID().'" name="'.$this->id.'">'.$this->data.'</textarea>';
	}
}

abstract class SF_TextBased extends SF_Element
{
	public function html()
	{
		$this->setDefaultAttributes();
		return "<input " . $this->generateAtrributes() . " />" . $this->appendedErrorMessage();
	}
}

class SF_TextField extends SF_TextBased
{
	public $minLength;
	public $maxLength;
	
	protected function setDefaultAttributes()
	{
		parent::setDefaultAttributes();
		$this->attributes['type'] = 'text';
	}
	
	public function validate()
	{
		parent::validate();
		if (strlen($this->data) < $this->minLength || strlen($this->data) > $this->maxLength)
		{
			throw new InputException("The length should lie between {$this->minLength} and {$this->maxLength}");
		}
	}
}

class SF_Password extends SF_TextField
{
	protected function setDefaultAttributes()
	{
		parent::setDefaultAttributes();
		$this->attributes['type'] = 'password';
		
		// Password need and should never be displayed again
		unset($this->attributes['value']);
	}
}

class SF_EMail extends SF_TextBased
{
	protected function setDefaultAttributes()
	{
		parent::setDefaultAttributes();
		$this->attributes['type'] = 'email';
	}
}

class SF_Number extends SF_TextBased
{
	public $min;
	public $max;
	public $step = 1;
	
	public function __construct()
	{
		call_user_func_array('parent::__construct',func_get_args());
		$this->attributes['type'] = 'number';
	}
	
	public function gatherFromPOST()
	{
		parent::gatherFromPOST();
		$this->data = intval($this->data);
	}
	
	public function validate()
	{
		parent::validate();
		if ($this->data < $this->min || $this->data > $this->max)
		{
			throw new InputException("The value must lie between {$this->min} and {$this->max}");
		}
	}
	
	public function html()
	{
		$this->attributes['min'] = $this->min;
		$this->attributes['max'] = $this->max;
		$this->attributes['step'] = $this->step;
		
		return parent::html();
	}
}


class SF_Date extends SF_TextBased
{
	protected function setDefaultAttributes()
	{
		parent::setDefaultAttributes();
		$this->attributes['type'] = 'date';
	}
}


class SF_DateTime extends SF_Date
{
	protected function setDefaultAttributes()
	{
		parent::setDefaultAttributes();
		$this->attributes['type'] = 'datetime';
	}
}

class SF_Checkbox extends SF_Element
{
	public function gatherFromPOST()
	{
		$this->data = isset($_POST[$this->id]);
	}
	
	public function html()
	{
		return "<input type='checkbox' id='".$this->getHTMLID()."' name='{$this->id}' " . ($this->data ? (' checked="checked"') : '') . $this->generateAtrributes() . " />" . $this->appendedErrorMessage();
	}
}

/*
class SF_RadioGroup extends SF_GroupElement
{
	
}

class SF_CheckboxGroup extends SF_GroupElement
{
	
}
*/
class SF_Select extends SF_Element
{
	protected $options;
	
	public function html()
	{
		$this->setDefaultAttributes();
		unset($this->attributes['value']);
		$html = '<select' . $this->generateAtrributes() . ">\n";
		foreach ($this->options as $label => $value)
		{
			$html .= '<option value="'.htmlspecialchars($value).'"'.($value == $this->data ? ' selected="selected"':'').'>'.htmlspecialchars($label).'</option>';
		}
		$html .= "\n</select>";
		return $html;
	}
}

?>