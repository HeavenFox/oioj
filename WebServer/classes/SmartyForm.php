<?php
/**
 * Smarty Form System
 * Create, validate and save form data in a consistent way
 * Works with ActiveRecord system
 */
class SmartyForm
{
	private $id;
	
	private $elements;
	
	private $activeRecords;
	
	private $recordAssoc;
	
	public $action;
	
	/**
	 * Is current form fresh, i.e. no data gathered?
	 *
	 * @var bool
	 */
	public $fresh;
	
	/**
	 * Whether or not to automatically append error message
	 */
	public $appendErrorMessage = true;
	
	/**
	 * Whether or not to validate fields on client side
	 */
	public $validateClientSide = false;
	
	/**
	 * Is current data valid?
	 * Value is defined only after validate method called
	 */
	public $valid;
	
	const HTML_ID_PREFIX = 'id-';
	
	public function __construct($id, $action)
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
	}
	
	public function getFormOpeningHTML()
	{
		$html = "<form id='sf_{$this->id}' method='post' action='".htmlspecialchars($this->action)."'>";
		foreach ($this->elements as $v)
		{
			if ($v instanceof SF_Hidden)
			{
				$html .= $v->html();
			}
		}
		return $html;
	}
	
	public function getHTML($ele)
	{
		return $this->elements[$ele]->html();
	}
	
	public function getLabelHTML($ele)
	{
		return '<label for="'.$this->elements[$ele]->getHTMLID() .'">'.$this->elements[$ele]->label.'</label>';
	}
	
	public function addRecord($id, $record)
	{
		$this->activeRecords[$id] = $record;
	}
	
	/**
	 * Bind an element with a property of ActiveRecord
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
	 * Gather data from HTTP POST request
	 */
	public function gatherFromPOST()
	{
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
		$this->elements = unserialize(IO::Session('sf-'.$this->id));
	}
	
	/**
	 * Gather data from Record
	 */
	public function gatherFromRecord()
	{
		foreach ($this->recordAssoc as $k => $v)
		{
			$prop = $v[2];
			if (isset($this->recordAssoc[$v[0]]->$prop))
				$this->elements[$k]->data = $this->recordAssoc[$v[0]]->$prop;
		}
	}
	
	/**
	 * Save gathered data to session
	 * Useful for multi-staged form
	 */
	public function saveToSession()
	{
		IO::SetSession('sf-'.$this->id, serialize($this->elements));
	}
	
	/**
	 * Save to ActiveRecord
	 */
	public function saveToRecord()
	{
		foreach ($this->recordAssoc as $k => $v)
		{
			$prop = $v[2];
			$this->recordAssoc[$v[0]]->$prop = $this->elements[$k]->data;
		}
	}
}

abstract class SF_Element
{
	private $validators;
	private $sanitizers;
	
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
	
	abstract function html();
	
	protected function appendedErrorMessage()
	{
		return ($this->form->appendErrorMessage && $this->errorMessage) ? $this->generateErrorHTML() : '';
	}
}

abstract class SF_GroupElement extends SF_Element
{
	
}

class SF_Hidden extends SF_Element
{
	public function html()
	{
		return "<input type='hidden' name='{$this->id}' value='{$this->data}' />";
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
	protected $htmltype;
	
	protected function extraAttributes()
	{
		return '';
	}
	
	public function html()
	{
		return "<input type='{$this->htmltype}' id='".$this->getHTMLID()."' name='{$this->id}' " . ($this->data ? (' value="'.htmlspecialchars($this->data).'"') : '') . $this->extraAttributes() . " />" . $this->appendedErrorMessage();
	}
}

class SF_TextField extends SF_TextBased
{
	protected $htmltype = 'text';
	
	public $minLength;
	public $maxLength;
	
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
	public function html()
	{
		// Password need and should never be displayed again
		return "<input type='password' id='".$this->getHTMLID()."' name='{$this->id}' />" . $this->appendedErrorMessage();
	}
}

class SF_EMail extends SF_TextBased
{
	protected $htmltype = 'email';
}

class SF_Number extends SF_TextBased
{
	public $min;
	public $max;
	public $step = 1;
	
	protected $htmltype = 'number';
	
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
	
	protected function extraAttributes()
	{
		$html = '';
		if ($this->min)
		{
			$html .= " min='{$this->min}'";
		}
		if ($this->max)
		{
			$html .= " max='{$this->max}'";
		}
		if ($this->step)
		{
			$html .= " step='{$this->step}'";
		}
		return $html;
	}
}


class SF_Date extends SF_TextBased
{
	protected $htmltype = 'date';
	protected function extraAttributes()
	{
		return '';
	}
}


class SF_DateTime extends SF_Date
{
	protected $htmltype = 'datetime';
	// Data is timestamp
	
}

class SF_Checkbox extends SF_Element
{
	public function gatherFromPOST()
	{
		$this->data = isset($_POST[$this->id]);
	}
	
	public function html()
	{
		return "<input type='checkbox' id='".$this->getHTMLID()."' name='{$this->id}' " . ($this->data ? (' checked="checked"') : '') . " />" . $this->appendedErrorMessage();
	}
}

/*
class SF_RadioGroup extends SF_GroupElement
{
	
}

class SF_CheckboxGroup extends SF_GroupElement
{
	
}

class SF_Select extends SF_GroupElement
{
	
}
*/
?>