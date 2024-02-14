import { useEffect } from "react";
import { generateNumberArray, updateQueryStringParameter, clearAllQueryParams, API_BASE_URL, buildFiltersUrlQueryParams, getUrlParamValueByKey, urlHaskey } from "../utils/helpers";
import { useState } from "react";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { SlClose, SlEye, SlList, SlNote, SlTrash } from "react-icons/sl";
import { useNavigate } from "react-router-dom";

import AccordianFilters from "../components/AccordianFilters";
import SwatchPagination from "../components/SwatchPagination";

export default function Swatches() {
  const [isLoading, setLoading] = useState(false);
  const [collections, setCollections] = useState([]);
  const [filters, setFilters] = useState([]);
  const [meta, setMeta] = useState([]);
  const [pages, setPages] = useState([0]);

  const Navigate = useNavigate();

  /*
  const storedSwatchesRequestUrl = localStorage.getItem("swatches_request_url");
  */

  const storedSwatchesRequestUrl = null;
  const initialSwatchesRequestUrl = storedSwatchesRequestUrl || API_BASE_URL + "swatches?source=all&limit=50&status=all";

  const [swatches_request_url, setsSatches_request_url] = useState(initialSwatchesRequestUrl);

  const [selectedFilters, setSelectedFilters] = useState([]);
  const [haveFilters, setHaveFilters] = useState(false);
  const [showFilters, setShowFilters] = useState(false);
  const [stockAccordian, setStockAccordian] = useState(false);
  const [listMeta, setListMeta] = useState([]);

  const [swatchSources, setSwatchSources] = useState([]);

  /*

  const [swatchSources, setSwatchSources] = useState([
    { name: "foxflannel", url: "foxflannel.com", active: true },
    { name: "loropiana", url: "loropiana.com", active: false },
    { name: "dugdalebros", url: "shop.dugdalebros.com", active: false },
    { name: "harrisons", url: "harrisons1863.com", active: false }
  ]);
  */

  const handleNavigate = (e, swatchId) => {
    Navigate(`/editswatch/${swatchId}`);
  };

  const handlePaginate = (e, pageNo) => {
    e.preventDefault();
    setsSatches_request_url((existingUrl) => updateQueryStringParameter(existingUrl, "page", pageNo));
  };

  const handleDelete = (swatchId) => {
    console.log("delete swatch with id of", swatchId);

    fetch(`${API_BASE_URL}swatches/${swatchId}`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: []
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((error) => {
            throw new Error(error.message || "Server error");
          });
        }
        return response.json();
      })
      .then((data) => {
        setCollections((prevCollections) => prevCollections.filter((swatch) => swatch.id !== swatchId));

        toast.success(data.message);
      })
      .catch((error) => {
        toast.error(error.message);
      });
  };

  const handleSource = (e, source) => {
    setSelectedFilters([]);

    e.preventDefault();
    var matchedSource = swatchSources.findIndex((item) => item.url == source);
    console.log(matchedSource);

    setsSatches_request_url((existingUrl) => {
      let clearedUrl = clearAllQueryParams(existingUrl);

      let appliedUrl = updateQueryStringParameter(clearedUrl, "source", source);
      appliedUrl = updateQueryStringParameter(appliedUrl, "limit", 50);
      appliedUrl = updateQueryStringParameter(appliedUrl, "status", "all");

      return appliedUrl;
    });

    setSwatchSources((prevSources) => {
      const updatedSources = prevSources.map((item) => {
        return {
          ...item,
          active: item.url === source
        };
      });
      return updatedSources;
    });
  };

  const handleSwatchStatusToggle = (swatchId, currentStatus) => {
    (async () => {
      const requestPayload = {
        operation: "status-toggle",
        status: !currentStatus
      };

      try {
        const response = await fetch(`${API_BASE_URL}swatches/${swatchId}`, {
          method: "PUT",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(requestPayload)
        });

        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.message);
        } else {
          const suceessData = await response.json();

          setCollections((previousCollections) => previousCollections.map((item) => (item.id === swatchId ? { ...item, status: !currentStatus } : item)));
        }
      } catch (error) {
        toast.error(error.message);
      }
    })();

    /*
    THIS IS A LONG VERSION OF UPDATING STATE LIST 
    return modified matching item else return unmodified
    setCollections((previousCollections) => {
      return previousCollections.map((item) => {
        if (item.id == swatchId) {
          return { ...item, status: !currentStatus };
        }
        return item;
      });
    });
    */
  };

  const clearAllFilters = () => {
    setSelectedFilters([]);

    setsSatches_request_url((existingUrl) => {
      let source = listMeta.source;

      let clearedUrl = clearAllQueryParams(existingUrl);

      let appliedUrl = updateQueryStringParameter(clearedUrl, "source", source);

      appliedUrl = updateQueryStringParameter(appliedUrl, "limit", 50);

      return appliedUrl;
    });
  };

  const applyFilters = () => {
    console.log("apply filter is called");

    let filterCopy = [...selectedFilters];

    setsSatches_request_url((existingUrl) => {
      let appendurlSourceAsFilterValue = { filterHeader: "source", values: [listMeta.source] };
      let filteringActivate = { filterHeader: "filteringActivate", values: ["on"] };

      filterCopy.push(appendurlSourceAsFilterValue, filteringActivate);

      let clearedUrl = clearAllQueryParams(existingUrl);
      return buildFiltersUrlQueryParams(clearedUrl, filterCopy);
    });

    setShowFilters(!showFilters);
  };

  const indicateActiveSource = (source) => {
    console.log("indicate source", source);

    console.log("swatch source", swatchSources);

    setSwatchSources((existingSource) => {
      return existingSource.map((sourceItem) => {
        return sourceItem.url == source ? { ...sourceItem, active: true } : sourceItem;
      });
    });
  };

  useEffect(() => {
    (async () => {
      setLoading(true);
      try {
        const response = await fetch(swatches_request_url);

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message);
        }

        const data = await response.json();

        if (data.collections.length > 0) {
          console.log("set colleciton");
          setCollections(data.collections);
        }

        if (data.meta.pages != undefined) {
          let generatedPagesArray = generateNumberArray(data.meta.pages);
          setPages((oldpages) => generatedPagesArray);
        }

        if (data.filters != undefined) {
          setFilters(response.filters);
        }

        if (data.sources != undefined && swatchSources.length == 0) {
          setSwatchSources(data.sources);
        }

        if (data.meta != undefined) {
          setListMeta(data.meta);
        }

        if (data.filters.length > 0) {
          setFilters(data.filters);
        }

        indicateActiveSource(data.meta.source);
      } catch (error) {
        console.dir(error);
      }

      localStorage.setItem("swatches_request_url", swatches_request_url);

      let isFilterACtive = !!urlHaskey(swatches_request_url, "filteringActivate");

      setHaveFilters(isFilterACtive);

      setLoading(false);
    })();
  }, [swatches_request_url]);

  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper bg-white">
          <div className="matched-info">Swatches :</div>
        </div>

        <div className="content-body  mt-10">
          <div className="dashContentWRap">
            <aside className="SwatchFilters">
              <div className="filterInner">
                <div className="filterList bg-white">
                  <div className="filter-labels">
                    <div>
                      <h5 className={`stock-list filter-accordion-header ${stockAccordian ? "active" : ""} `} onClick={() => setStockAccordian(!stockAccordian)}>
                        - STOCK COLLECTIONS :
                      </h5>
                      <ul className="filter-list-items">
                        {swatchSources.map((source, index) => (
                          <li key={index} className={`${source.active ? "checkedFilterItem" : ""}`} onClick={(e) => handleSource(e, source.url)}>
                            {source.name}
                          </li>
                        ))}
                      </ul>
                    </div>
                  </div>

                  {listMeta.source != "all" && <AccordianFilters filters={filters} setFilters={setFilters} selectedFilters={selectedFilters} setSelectedFilters={setSelectedFilters} />}
                </div>

                <div className="swatch_apply_filters">
                  <div className="flashButtonWrapper">
                    <div className="applyFilterBtn text_btn_lg" onClick={applyFilters}>
                      APPLY FILTERS
                    </div>
                  </div>

                  {haveFilters && (
                    <div className="clearFilterBT_wrap" onClick={clearAllFilters}>
                      <div className="clearOutlineBtFilter">
                        <div className="filerBTtext">
                          <SlClose className="clear-filter-icon" />
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </aside>

            <div className="swatches-dash-list">
              <div className="pageStatusInfo">
                <div className="pagesCount bg-white">
                  Items : {(listMeta.page - 1) * parseInt(listMeta.limit) + 1} - {listMeta.page == listMeta.pages ? listMeta.total : collections.length * listMeta.page} / {listMeta.total}
                </div>
                <SwatchPagination pages={pages} listMeta={listMeta} handlePaginate={handlePaginate} />
              </div>

              <div className="list-items-table bg-white">
                <table>
                  <thead>
                    <tr>
                      <th>ID </th>
                      <th>Image</th>
                      <th>Title</th>
                      <th>Status</th>

                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                    </tr>
                  </thead>

                  <tbody>
                    {collections.length &&
                      collections.map((swatch) => (
                        <tr key={swatch.id}>
                          <td>{swatch.id}</td>
                          <td>
                            <img src={`${API_BASE_URL}/${swatch.thumbnail}`} width="50" height={50} />
                          </td>
                          <td>{swatch.title}</td>

                          <td>
                            <label className="switch" htmlFor={`checkbox-user-${swatch.id}`}>
                              <input
                                type="checkbox"
                                className="user-check-toggle"
                                id={`checkbox-user-${swatch.id}`}
                                value={swatch.status}
                                checked={swatch.status == 1}
                                onChange={() => handleSwatchStatusToggle(swatch.id, swatch.status)}
                              />
                              <div className="slider round"></div>
                            </label>
                          </td>

                          <td>
                            <div onClick={(e) => handleNavigate(e, swatch.id)}>
                              <SlNote className="edit-icon" />
                            </div>
                          </td>
                          <td>
                            <SlTrash className="delete-icon" onClick={() => handleDelete(swatch.id)} />
                          </td>
                        </tr>
                      ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </main>
      <ToastContainer />
    </>
  );
}
