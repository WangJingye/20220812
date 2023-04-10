<?php
/**
 * TOP API: 154o3iz55l.connext.order.newview.bdm request
 *
 * @author auto create
 * @since 1.0, 2019.11.28
 */
class ConnextRequest
{
    private $apiParas = array();

	private $checkParams = ['endTime', 'page', 'size', 'startTime'];

	private $apiName = '';

	public function __construct(string $apiName, array $checkParams = [])
    {
        $this->apiName = $apiName;
        if (!empty($checkParams))
            $this->checkParams = $checkParams;
    }

    public function __set($name, $value)
    {
        $name = lcfirst($name);
        $this->$name = $value;
        $this->apiParas[ucfirst($name)] = $value;
    }

    public function __get($name)
    {
        $name = lcfirst($name);
        return $this->$name;
    }

	public function getApiMethodName()
	{
	    return $this->apiName;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function check()
	{

		RequestCheckUtil::checkNotNull($this->endTime,"endTime");
		RequestCheckUtil::checkNotNull($this->page,"page");
		RequestCheckUtil::checkNotNull($this->size,"size");
		RequestCheckUtil::checkNotNull($this->startTime,"startTime");
	}
}
