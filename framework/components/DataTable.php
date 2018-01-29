<?php
/**
 * DataTable.php
 * Renders a DataTable
 */
namespace components;
class DataTable {

	protected $_header;
	protected $_result;
	protected $_pkCol;
	protected $_path = null;
	protected $_cond;
	protected $_highlight;
	protected $col;
	protected $_dataTableEnable = null;

	/**
	 * Sets the title of this table
	 * @param string $header
	 */
	public function setTableHeader($header)
	{
		$this->_header = $header;
		return $this;
	}

	/**
	 * Set the contents of this table
	 * @param array $array
	 */
	public function setTableContents($array)
	{
		$this->_result = $array;
		return $this;
	}

	/**
	 * Whether to include DataTable search and sort functionality
	 */
	public function isDataTable()
	{
		$this->_dataTableEnable = true;
		return $this;
	}

	/**
	 * Hides the primary key field from the result set, but also uses
	 * the value of this field to help form the contents of the setRowClickLink method
	 * @param string $value
	 */
	public function setPrimaryKeyField($value)
	{
		$this->_pkCol = $value;
		return $this;
	}

	/**
	 * Set the row click link of every row.
	 * @param string $link
	 */
	public function setRowClickLink($link)
	{
		$this->_path = $link;
	}
	/**
	 * Evaluates an expression and the table row highlights to a colour
	 * of your choice
	 * @param string $cond      A PHP expression which evaluates to true (At the moment only double equals conditions are supported).
	 * @param [type] $highlight A chosen colour in hexadecimal notation to highlight.
	 */
	public function setConditionToHighlight($cond,$highlight)
	{
		$this->_cond = $cond;
		$this->_highlight = $highlight;
	}

	/**
	 * Defines a group of columns or 1 column to be hidden from
	 * the rendered table
	 * @param mixed $col An array of column names or string of a column name
	 */
	public function setCellHidden($col)
	{
		if(is_array($col)) {
			foreach($col as $eachCol) {
				$this->col[$eachCol] = $eachCol;
			}
		} else {
			$this->col[$col] = $col;
		}
		return $this;
	}
	
	/**
	 * Evaluates the condition and highlights the cell.
	 * @param  string $value
	 * @return string
	 */
	protected function _processCondition($value)
	{

		if(isset($this->_cond) && isset($this->_highlight))
		{
			$split = explode(" ",$this->_cond);
			$matchCol = trim($split[0]);
			$matchVal = trim(str_replace("'","",$split[2]) );
			if($split[1] == "==")
			{
				if($value[$matchCol] == $matchVal)
				{
					return "style=\"background-color:$this->_highlight;\"";
				}
			}
		}
		return false;
	}

	/**
	 * Hides the cell
	 * @param  string $col
	 * @return mixed
	 */
	protected function _evaluateCell($col)
	{
		if(is_null($this->col) || empty($this->col))
			return false;

		if(in_array($col,$this->col)) return "hidden-lg hidden-md hidden-xs hidden-sm";
		return false;
	}

	/**
	 * Renders Table
	 * @return string html for this table
	 */
	public function generate()
	{
		$result = (array)$this->_result;
		
 		$html = "<h2>$this->_header</h2><table class=\"results table table-striped table-responsive table-hover\">
					<thead>";
		$firstIteration = false;
		foreach($result as $key => $value )
		{

			if(!$firstIteration)
			{
				$html.="<tr class=\"warning\">";
				foreach($value as $col => $val)
				{

					if($col == $this->_pkCol)
						continue;

					$isHidden = $this->_evaluateCell($col);
					$col = \components\StringUtils::convertColNameToString($col);
					$html .= "<td class=\"td $isHidden\">$col</td>";
				}
				$html.="</tr>";
				$firstIteration = true;
			}
		}
		$html.= "<tbody>";
		foreach($result as $key => $value )
		{
			$curRowId = $value->{$this->_pkCol};
			
			$html.="<tr data-row-id=\"$curRowId\" ". $this->_processCondition($value) .">";
			foreach($value as $col => $val)
			{

				if($col == $this->_pkCol)
					continue;
				$isHidden = $this->_evaluateCell($col);	
				$val = \components\StringUtils::convertColNameToString($val);
				$html .= "<td class=\"td $isHidden\">$val</td>";
			}
			$html.="</tr>";
		}
		$html.="</tbody></table>";

		if(!is_null($this->_dataTableEnable) || !empty($this->_dataTableEnable))
		{
			$html.="
			<script>

				$(document).ready(function(){
				    $('.results').DataTable();
				});
			</script>
			";
		}

		if(!is_null($this->_path) || !empty($this->_path))
		{
			$html.="<script>	    $('.results tbody td').on(\"click\", function() {
				    	\$tr = $(this).closest(\"tr\").data(\"row-id\");
				    	window.location.href = host + \"" . $this->_path . "?id=\" + \$tr ;
				});</script>";
		}

		$html .= "</script>";
		// if(is_null($this->_path) || empty($this->_path))
		// {
		// 	$html .="</script>";
		// }
		// else
		// {
		// 	$html .= "});</script>";
		//}

		return $html;
	}

}
