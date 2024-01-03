<?php
class swatchesModule
{

    public $DB;

    public function __construct()
    {

        $this->DB = new Database();
        $this->DB->table = 'swatches';
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



    public function getSwatces($payload = null)
    {

        if ($payload) {
            extract($payload);
        }


        $query = "SELECT SQL_CALC_FOUND_ROWS id, title, imageUrl, productPrice, productMeta, source, status from swatches WHERE status = 1";


        if (isset($source)) {
            $string = " source = '{$source}'";
            $query .= $this->appendQuery($query, $string);
        }


        if (isset($colors)) {
            $colorsString = $colors;
            $colorsArray = explode(',', $colorsString);
            $quotedColorsArray = array_map(function ($color) {
                return "'" . $color . "'";
            }, $colorsArray);

            // Implode the array to get the final string
            $formattedColorsString = implode(',', $quotedColorsArray);
            $query .= " AND JSON_EXTRACT(productMeta, '$.COLOUR')  IN ($formattedColorsString) ";
        }


        if (isset($pattern)) {
            $patternString = $pattern;
            $patternArray = explode(',', $patternString);
            $quotedPatternArray = array_map(function ($pattern) {
                return "'" . $pattern . "'";
            }, $patternArray);

            // Implode the array to get the final string
            $formattedPatternString = implode(',', $quotedPatternArray);

            $query .= " AND JSON_EXTRACT(productMeta, '$.PATTERN')  IN ($formattedPatternString) ";
        }

        /*
    METRIC WEIGHT
    
    */

        if (isset($metricWeight)) {
            $metricWeightString = $metricWeight;
            $metricWeightArray = explode(',', $metricWeightString);

            $metricWeightArrayQuotted = array_map(function ($metricWeight) {
                return "'" . $metricWeight . "'";
            }, $metricWeightArray);

            // Implode the array to get the final string
            $metricWeightFormatted = implode(',', $metricWeightArrayQuotted);

            $query .= " AND JSON_EXTRACT(productMeta, '$.\"METRIC WEIGHT\"') IN ($metricWeightFormatted) ";
        }

        if (isset($imperialWeight)) {

            $imperialWeightArray = explode(',', $imperialWeight);

            $imperialWeightArrayQuotted = array_map(function ($imperialWeight) {
                return "'" . $imperialWeight . "'";
            }, $imperialWeightArray);

            // Implode the array to get the final string
            $imperialWeightFormatted = implode(',', $imperialWeightArrayQuotted);

            $query .= " AND JSON_EXTRACT(productMeta, '$.\"IMPERIAL WEIGHT\"') IN ($imperialWeightFormatted) ";
        }

        $query .= " LIMIT {$offset},  {$limit}";


        $stmt = $this->DB->connection->prepare($query);
        /*       
    $stmt->bind_param('s', $source);
    */
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getSwatchByID($source)
    {

        $query = "SELECT SQL_CALC_FOUND_ROWS id, title, imageUrl, productPrice, productMeta, source, status from swatches where source = ?";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('s', $source);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all();
    }



    public function getFilterColors()
    {

        $query = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$.COLOUR')) AS colours
        FROM swatches where source = 'foxflannel.com'";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getFoxDistinctPatterns()
    {

        $query = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$.PATTERN')) AS patterns
        FROM swatches where source = 'foxflannel.com'";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getFoxMetricWeightDistinct()
    {

        $query = "SELECT DISTINCT 
        JSON_UNQUOTE(JSON_EXTRACT(productMeta, '\$.\"METRIC WEIGHT\"')) 
        AS metric_weight
        FROM swatches WHERE SOURCE = 'foxflannel.com'";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getFoxImperialWeightDistinct()
    {

        $query = "SELECT DISTINCT 
        JSON_UNQUOTE(JSON_EXTRACT(productMeta, '\$.\"IMPERIAL WEIGHT\"')) 
        AS imperial_weight
        FROM swatches WHERE SOURCE = 'foxflannel.com'";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function foxColourFilters()
    {

        $colorFilters = [];
        $colorFiltersCollection = $this->getFilterColors();
        foreach ($colorFiltersCollection as $key => $value) {

            array_push($colorFilters, $value['colours']);
        }
        return $colorFilters;
    }

    public function getFoxPatternFilter()
    {

        $pattenrs = [];

        $patternCollection = $this->getFoxDistinctPatterns();

        foreach ($patternCollection as $key => $value) {

            if ($value['patterns'] != "") {
                array_push($pattenrs, $value['patterns']);
            }
        }

        return $pattenrs;
    }

    public function getMetricWeightFilters()
    {

        $metricWeight = [];
        $metricWeightCollection = $this->getFoxMetricWeightDistinct();


        foreach ($metricWeightCollection as $key => $value) {
            if ($value['metric_weight'] != "") {

                array_push($metricWeight, $value['metric_weight']);
            }
        }

        return $metricWeight;
    }


    public function getImperialWeightFilters()
    {

        $imperialWeight = [];
        $imperialWeightCollection = $this->getFoxImperialWeightDistinct();


        foreach ($imperialWeightCollection as $key => $value) {

            if ($value['imperial_weight'] != "") {
                array_push($imperialWeight, $value['imperial_weight']);
            }
        }

        return $imperialWeight;
    }


    private function getUniqueFilterKeys($source)
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



    private function getUniqueFilterValues($filterKey, $source)
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

        if ($uniqueFilterKeys = $this->getUniqueFilterKeys($source)) {

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


            $rawFilter = [];
            $cleanFilter = [];

            foreach ($filterHeader as $key => $filterKey) {

                $rawFilter[$filterKey] = $this->getUniqueFilterValues($filterKey, $source);
            }

            foreach ($rawFilter as $key => $values) {
                $items = array_column($values, 'key_value');

                $cleanFilter[] = [
                    'name' => $key,
                    'items' => array_values(array_unique($items))
                ];
            }
            return $cleanFilter;
        } else {

            return false;
        }
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