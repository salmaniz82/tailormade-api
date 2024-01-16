import { useEffect } from "react";
import { API_BASE_URL } from "../utils/helpers";
import { useState } from "react";

export default function Swatches() {
  const [isLoading, setLoading] = useState(false);

  const [swatches, setSwatches] = useState([]);

  const [collections, setCollections] = useState([]);
  const [filters, setFilters] = useState([]);
  const [meta, setMeta] = useState([]);

  const handleSwatchStatusToggle = (swatchId, currentStatus) => {
    /*
    setCollections((previousCollections) => {
      return previousCollections.map((item) => {
        if (item.id == swatchId) {
          return { ...item, status: !currentStatus };
        }

        return item;
      });
    });
    */

    setCollections((previousCollections) =>
      previousCollections.map((item) =>
        item.id === swatchId ? { ...item, status: !currentStatus } : item
      )
    );
  };

  useEffect(() => {
    (async () => {
      setLoading(true);
      try {
        const response = await fetch(
          API_BASE_URL + "swatches?limit=300&source=loropiana.com&status=all"
        );

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message);
        }

        const data = await response.json();

        if (data.collections.length > 0) {
          console.log("set colleciton");
          setCollections(data.collections);
        }

        if (data.meta.length > 0) {
          setMeta(data.meta);
        }

        if (data.filters.length > 0) {
          setFilters(data.filters);
        }

        console.log("success data", data);
      } catch (error) {
        console.dir(error);
      }

      setLoading(false);
    })();
  }, []);

  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper bg-white">
          <h3 className="page-title"> Swatches : {collections.length} </h3>
        </div>

        <div className="content-body bg-white mt-10">
          <div className="SWatchs"></div>

          <div>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>

                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>

              <tbody>
                {collections.length &&
                  collections.map((swatch) => (
                    <tr key={swatch.id}>
                      <td>{swatch.id}</td>
                      <td>{swatch.title}</td>

                      <th>
                        <label
                          className="switch"
                          htmlFor={`checkbox-user-${swatch.id}`}
                        >
                          <input
                            type="checkbox"
                            className="user-check-toggle"
                            id={`checkbox-user-${swatch.id}`}
                            value={swatch.status}
                            checked={swatch.status == 1}
                            onChange={() =>
                              handleSwatchStatusToggle(swatch.id, swatch.status)
                            }
                          />
                          <div className="slider round"></div>
                        </label>
                      </th>
                      <th>menu</th>
                    </tr>
                  ))}
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </>
  );
}
