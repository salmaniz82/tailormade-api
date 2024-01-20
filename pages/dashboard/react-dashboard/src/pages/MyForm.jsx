import React, { useState, useEffect } from "react";
import { API_BASE_URL } from "../utils/helpers";
import Select from "react-select";

function MyForm() {
  const [getMetaData, setMetaData] = useState([]);

  const fetchMetaData = async () => {
    try {
      const response = await fetch(`${API_BASE_URL}swatchemeta`);

      const responseData = await response.json();

      console.log(responseData.metadata);

      if (responseData.metadata != undefined && response.ok) {
        setMetaData(responseData.metadata);
      }

      // Do something with the response, e.g., parse JSON, update state, etc.
    } catch (error) {
      // Handle errors
      console.error("Error fetching data:", error);
    }
  };

  const handleSelectChange = (selectedOption) => {
    // Access the metaFields array of the selected option
    const selectedMetaFields = selectedOption.metaFields;
    console.log(selectedMetaFields);
    // Add any further logic or state updates based on the selected option
  };

  const options = getMetaData.map((meta) => ({
    value: meta.url,
    label: meta.title,
    metaFields: meta.metaFields
  }));

  useEffect(() => {
    fetchMetaData();
  }, []);

  return (
    <form action="">
      <div className="dfx">
        <div className="fieldWrap">
          <div className="Field">
            <label htmlFor="title">Title:</label>
            <input type="text" name="title" id="title" />
          </div>
        </div>

        <div className="fieldWrap">
          <div className="Field">
            <label htmlFor="status">Status</label>
            <input type="checbox" name="status" id="status" />
          </div>
        </div>
      </div>

      <div className="dfx">
        <div className="fieldWrap">
          <div className="Field">
            <Select options={options} onChange={handleSelectChange} />
          </div>
        </div>
      </div>
    </form>
  );
}

export default MyForm;
