<?php

namespace App\Modules;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use \Framework\Database;

class swatchesModule
{

    public $DB;
    private $swatchParams;

    public function __construct()
    {
        $this->DB = new Database();
        $this->DB->table = 'swatches';
    }




    public function delete($id)
    {

        $query = "DELETE FROM swatches where id = ? LIMIT 1";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('d', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }



    public function getSwatchMeta()
    {

        $query = "SELECT id, title, url, alias, metaFields from stocks";
        $stmt = $this->DB->connection->prepare($query);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function pluckAliasviaSource($source)
    {

        $query = "SELECT alias from stocks WHERE url = '{$source}' LIMIT 1";
        $stmt = $this->DB->connection->prepare($query);
        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data[0]['alias'];
        }
    }






    public function update($payload, $id)
    {

        if ($this->DB->update($payload, $id)) {

            return true;
        }

        return false;
    }

    public function appendQuery($query, $string)
    {


        if (strpos($query, 'WHERE') !== false) {
            $queryString = ' AND ' . $string;
        } else {
            $queryString = ' WHERE ' . $string;
        }
        return urldecode($queryString);
    }


    public function getTotalMatched()
    {
        $sqlTotal = "SELECT FOUND_ROWS() as 'totalMatched'";
        $totalRecords = $this->DB->rawSql($sqlTotal)->returnData();
        return $totalRecords[0]['totalMatched'];
    }


    public function addSwatch($dataPayload)
    {

        if ($lastId = $this->DB->insert($dataPayload)) {
            return $lastId;
        }

        return false;
    }





    public function getSourceFilterStaticKeys($source, $clean = false)
    {

        $outputArrayKeys = [];

        if ($source == 'foxflannel.com')
            $outputArrayKeys = ['COLOUR', 'PATTERN', 'METRIC_WEIGHT', 'IMPERIAL_WEIGHT'];

        if ($source == 'shop.dugdalebros.com')
            $outputArrayKeys = ['material', 'Width', 'Weight', 'Bunch_Name', 'Bunch_Number'];

        if ($source == 'harrisons1863.com')
            $outputArrayKeys = ['brand', 'color', 'weight', 'material', 'collection', 'stockLength', 'longestSingleLength'];

        if ($source == 'loropiana.com')
            $outputArrayKeys = ['size', 'Width', 'bunch', 'style', 'Family', 'Weight', 'Subfamily', 'Composition', 'Weight_g/_mu00b2', 'Mood'];

        if ($clean == true) {
            $withoutUnderScore = array_map(function ($value) {
                return str_replace('_', ' ', $value);
            }, $outputArrayKeys);

            return $withoutUnderScore;
        }

        return $outputArrayKeys;
    }




    public function getSwatces($payload = null)
    {

        if ($payload) {
            $this->swatchParams = $payload;
            extract($payload);
        }

        $query = "SELECT SQL_CALC_FOUND_ROWS id, title, imageUrl, thumbnail, productPrice, productMeta, 
                    source, status from swatches ";

        if (isset($source)) {
            $string = " source = '{$source}'";
            $query .= $this->appendQuery($query, $string);
        }

        if (isset($status) && $status == 'all') {
            $string =  " status IN (0, 1) ";
            $query .= $this->appendQuery($query, $string);
        } else {
            $string =  " status = 1 ";
            $query .= $this->appendQuery($query, $string);
        }





        if ($filteringActivate == 'on') :

            $filterParamsKeys = $this->getSourceFilterStaticKeys($source);
            $this->applyFilters($filterParamsKeys, $query);

        endif;

        $query .= " AND  trashed =  0 ";

        $query .= " LIMIT {$offset},  {$limit}";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function applyFilters($filterKeys, &$query)
    {

        foreach ($filterKeys as $filter) {
            if (isset($this->swatchParams[$filter])) {
                $this->swatchParams[$filter];
                $jsonPathKey = str_replace('_', ' ', $filter);
                $query .= $this->buildFilterQueryArgsString($jsonPathKey, $this->swatchParams[$filter]);
            }
        }
    }

    public function buildFilterQueryArgsString($metaKey, $metaMatchTerms)
    {

        /*
        $static = " AND JSON_EXTRACT(productMeta, '$.\"KEY NAME\"') IN ($formattedTermString) ";  
        */

        $metaMatchTermsArray = explode(',', $metaMatchTerms);
        $quotedTermsArray = array_map(function ($metaMatchTerms) {
            return "'" . $metaMatchTerms . "'";
        }, $metaMatchTermsArray);

        $formattedTermString = implode(',', $quotedTermsArray);
        $jsonPath = '$."' . $metaKey . '"';
        $query = " AND JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$jsonPath')) IN ($formattedTermString) ";
        return $query;
    }


    public function getSwatchByID($id)
    {

        $query = "SELECT id, title, imageUrl, thumbnail, productPrice, productMeta, source, status from swatches where id = ? LIMIT 1";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('d', $id);
        if (!$stmt->execute()) {
            return false;
        }

        $result = $stmt->get_result();
        $output = $result->fetch_all(MYSQLI_ASSOC);

        if (sizeof($output) > 0) {
            return $output[0];
        }

        return false;
    }




    public function getUniqueFilterKeys($source)
    {

        $query = "SELECT DISTINCT JSON_UNQUOTE(JSON_KEYS(productMeta)) AS key_name
        FROM swatches
        WHERE source = ? AND status = 1";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('s', $source);
        if ($stmt->execute()) {

            $result = $stmt->get_result();
            return $result->fetch_all();
        }

        return false;
    }



    public function getUniqueFilterValues($filterKey, $source)
    {
        $query = "SELECT DISTINCT 
            JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$.\"{$filterKey}\"')) 
            AS key_value
            FROM swatches WHERE SOURCE = '{$source}' AND status = 1";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function recursiveMergeUnique($arrays)
    {
        $result = [];

        foreach ($arrays as $array) {
            if (is_array($array)) {
                $result = array_merge($result, $this->recursiveMergeUnique($array));
            } else {
                $result[] = $array;
            }
        }

        return array_unique($result);
    }

    public function buildFilterDynamic($source)
    {

        if ($source == null)
            return false;

        $filterHeader = [];

        /*
            this fetches from DB
            $this->getUniqueFilterKeys($source)

            test this
            $this->getSourceFilterStaticKeys($source, true)
        */

        if ($uniqueFilterKeys = $this->getSourceFilterStaticKeys($source, true)) {

            /*

            used when picked dynamic

            if (sizeof($uniqueFilterKeys) > 1) {
                foreach ($uniqueFilterKeys as $key => $value) {
                    $uniqueFilterHeader = json_decode($value[0]);
                    array_push($filterHeader, $uniqueFilterHeader);
                    $mergedUniqeArray = $this->recursiveMergeUnique($filterHeader);
                    $filterHeader = $mergedUniqeArray;
                }
            } else {

                $filterHeader = json_decode($uniqueFilterKeys[0][0]);
            }
            */

            $filterHeader = $uniqueFilterKeys;
            if ($source == 'shop.dugdalebros.com') {
                $filterHeader = array_diff($filterHeader, ['Product SKU']);
            }

            if ($source == 'loropiana.com') {
                $filterHeader = array_diff($filterHeader, ['style']);
            }


            $rawFilter = [];
            $cleanFilter = [];

            foreach ($filterHeader as $key => $filterKey) {

                $rawFilter[$filterKey] = $this->getUniqueFilterValues($filterKey, $source);
            }

            foreach ($rawFilter as $key => $values) {
                // Filter out empty keys and values
                if (trim($key) === '' || empty($values)) {
                    continue;
                }

                $items = array_column($values, 'key_value');
                $items = array_values(array_unique(array_filter($items, 'trim')));
                if (!empty($items)) {
                    $cleanFilter[] = [
                        'name' => $key,
                        'items' => $items
                    ];
                }
            }

            return $cleanFilter;
        } else {

            return false;
        }
    }





    public function getCachedFilters($source)
    {


        $cacheDir = ABSPATH . 'cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $filename = "filer-$source.json";
        $cacheFile = $cacheDir . $filename;

        if (file_exists($cacheFile)) {
            // Load from cache file
            $filterData = json_decode(file_get_contents($cacheFile), true);
        } else {
            $filterData = [];
        }


        return $filterData;
    }

    public function buildCachedFilters($source)
    {

        $cacheDir = ABSPATH . 'cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $filename = "filer-$source.json";
        $cacheFile = $cacheDir . $filename;


        $filterData = $this->buildFilterDynamic($source);
        if (file_put_contents($cacheFile, json_encode($filterData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)))
            echo "file created successfully";
    }
}


/*


GET LIST OF ALL DISTINCT COLOR IN foxflannel.com
------------------------------------------------
SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$.COLOUR')) AS distinct_color
FROM swatches where source = 'foxflannel.com';


GET LIST OF ALL DISTINCT PATTERN 
SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$.PATTERN')) AS distinct_pattern
FROM swatches where source = 'foxflannel.com';


METRIC WEIGHT
------------
SELECT DISTINCT
    JSON_UNQUOTE(
        JSON_EXTRACT(productMeta, '$."METRIC WEIGHT"')
    ) AS distinct_pattern
FROM
    swatches
WHERE SOURCE = 'foxflannel.com';

*/