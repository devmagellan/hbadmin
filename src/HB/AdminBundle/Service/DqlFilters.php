<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Service;


class DqlFilters
{
    /**
     * @var int
     */
    private $aliasIterator = 1;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var array
     */
    private $dqlArray = [];

    /**
     * @var array
     */
    private $requestParameters;

    public function __construct(array $parametersArray)
    {
        $this->requestParameters = $parametersArray;

        return $this;
    }

    public function like($dqlParam, $paramKey)
    {
        if (!$this->checkParameter($paramKey)) return $this;

        $alias = $this->getParameterAlias();

        $this->dqlArray[] = 'LOWER('.$dqlParam.') LIKE '.$alias;
        $this->parameters[$alias] = "%".trim(mb_strtolower($this->requestParameters[$paramKey]))."%";

        return $this;
    }

    public function likeOrLike(string $dqlParam1, string $dqlParam2, string $paramKey)
    {
        if (!$this->checkParameter($paramKey)) return $this;

        $alias = $this->getParameterAlias();

        $this->dqlArray[] = '( LOWER('.$dqlParam1.') LIKE '.$alias.' OR LOWER('.$dqlParam2.') LIKE '.$alias.') ';
        $this->parameters[$alias] = "%".trim(mb_strtolower($this->requestParameters[$paramKey]))."%";

        return $this;
    }

    public function greaterOrEqualNumeric($dqlParam, $paramKey)
    {
        if (!$this->checkNumericParameter($paramKey)) return $this;
        $this->simpleCompare($dqlParam, $this->requestParameters[$paramKey], '>=');

        return $this;
    }

    public function greaterOrEqual($dqlParam, $paramKey)
    {
        if (!$this->requestParameters[$paramKey] || $this->requestParameters[$paramKey] === '') return $this;

        $this->simpleCompare($dqlParam, $this->requestParameters[$paramKey], '>=');

        return $this;
    }

    public function greaterNumeric($dqlParam, $paramKey)
    {
        if (!$this->checkNumericParameter($paramKey)) return $this;
        $this->simpleCompare($dqlParam, $this->requestParameters[$paramKey], '>');

        return $this;
    }

    public function lessOrEqualNumeric($dqlParam, $paramKey)
    {
        if (!$this->checkNumericParameter($paramKey)) return $this;
        $this->simpleCompare($dqlParam, $this->requestParameters[$paramKey], '<=');

        return $this;
    }

    public function lessOrEqual($dqlParam, $paramKey)
    {
        if (!$this->requestParameters[$paramKey] || $this->requestParameters[$paramKey] === '') return $this;

        $this->simpleCompare($dqlParam, $this->requestParameters[$paramKey], '<=');

        return $this;
    }

    public function lessNumeric($dqlParam, $paramKey)
    {
        if (!$this->checkNumericParameter($paramKey)) return $this;
        $this->simpleCompare($dqlParam, $this->requestParameters[$paramKey], '<');

        return $this;
    }

    public function equalNumeric($dqlParam, $paramKey)
    {
        if (!$this->checkNumericParameter($paramKey)) return $this;
        $this->simpleCompare($dqlParam, $this->requestParameters[$paramKey], '=');

        return $this;
    }

    public function greaterTimestamp($dqlParam, $paramKey)
    {
        if (!$this->checkNumericParameter($paramKey)) return $this;

        $date = new \DateTime();
        $date->setTimestamp(time() - $this->requestParameters[$paramKey]);
        $this->simpleCompare($dqlParam, $date, '>');

        return $this;
    }

    public function greaterTimestampHours($dqlParam, $paramKey)
    {
        if (!$this->checkNumericParameter($paramKey)) return $this;

        $timestamp = 60 * 60 * $this->requestParameters[$paramKey];
        $date = new \DateTime();
        $date->setTimestamp(time() - $timestamp);
        $this->simpleCompare($dqlParam, $date, '>');

        return $this;
    }

    public function equal($dqlParam, $paramKey)
    {
        if (!$this->checkParameter($paramKey)) return $this;
        $this->simpleCompare('LOWER('.$dqlParam.')', trim(mb_strtolower($this->requestParameters[$paramKey])), '=');

        return $this;
    }

    public function in($dqlParam, array $values)
    {
        if (empty($values)) return $this;

        $str = implode(',', $values);
        $this->dqlArray[] = $dqlParam.' IN ('.$str.') ';

        return $this;
    }

    public function isNullOrNotNull($dqlParam, $paramKey)
    {
        if (!$this->checkParameter($paramKey)) return $this;

        if ($this->requestParameters[$paramKey] == '1') {
            $this->dqlArray[] = $dqlParam.' IS NOT NULL';
        } else if ($this->requestParameters[$paramKey] == '0') {
            $this->dqlArray[] = $dqlParam.' IS NULL';
        }

        return $this;
    }

    public function trueOrNullFalse($dqlParam, $paramKey)
    {
        if (!$this->checkParameter($paramKey)) return $this;

        if ($this->requestParameters[$paramKey] == '1') {
            $this->dqlArray[] = $dqlParam.' = true ';
        } else if ($this->requestParameters[$paramKey] == '0') {
            $this->dqlArray[] = '('.$dqlParam.' IS NULL OR '.$dqlParam.' = false ) ';
        }

        return $this;
    }

    public function orEqual($dqlParam1, $compareSymbol1, $param1, $dqlParam2, $compareSymbol2, $param2)
    {
        if (is_null($param1) or is_null($param2)) return $this;

        $dql1 = ' '.$dqlParam1.' '.$compareSymbol1.' '.$param1;
        $dql2 = ' '.$dqlParam2.' '.$compareSymbol2.' '.$param2;
        $this->dqlArray[] = ' ( '.$dql1.' OR '.$dql2.' ) ';

        return $this;
    }

    private function simpleCompare($dqlParam, $param, $compareSign)
    {
        $alias = $this->getParameterAlias();
        $this->dqlArray[] = $dqlParam.' '.$compareSign.' '.$alias;
        $this->parameters[$alias] = $param;
    }

    private function checkParameter($paramKey)
    {
        if (isset($this->requestParameters[$paramKey]) and $this->requestParameters[$paramKey] != '') {
            return true;
        }
        return false;
    }

    private function checkNumericParameter($paramKey)
    {
        if (!$this->checkParameter($paramKey)) {
            return false;
        }
        if (is_numeric($this->requestParameters[$paramKey])) {
            return true;
        }
        return false;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getDql($prefix = 'WHERE', $logicOperator = 'AND')
    {
        $dql = '';
        if (!empty($this->dqlArray)) {
            $dql = ' '.$prefix.' '.implode(' '.$logicOperator.' ', $this->dqlArray).' ';
        }
        return $dql;
    }

    private function getParameterAlias()
    {
        $alias = ':p'.$this->aliasIterator;
        $this->aliasIterator++;

        return $alias;
    }

    public function unsetAll()
    {
        $this->dqlArray = $this->requestParameters = $this->parameters = [];
    }
}