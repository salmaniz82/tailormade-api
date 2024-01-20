import React, { useState, useEffect } from "react";
import { API_BASE_URL } from "../utils/helpers";
import Select from "react-select";
import { SlClose } from "react-icons/sl";

function MyForm() {
  const [getMetaData, setMetaData] = useState([]);
  const [selectedOption, setSelectedOption] = useState(null);
  const [formData, setFormData] = useState([]);
  const [newKey, setNewKey] = useState("");
  const [newValue, setNewValue] = useState("");

  const fetchMetaData = async () => {
    try {
      const response = await fetch(`${API_BASE_URL}swatchemeta`);
      const responseData = await response.json();

      console.log(responseData.metadata);

      if (responseData.metadata !== undefined && response.ok) {
        setMetaData(responseData.metadata);
      }
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  };

  const handleSelectChange = (selectedOption) => {
    const selectedMetaFields = JSON.parse(selectedOption.metaFields);

    const initialFormData = selectedMetaFields.map((field) => ({
      key: field,
      value: ""
    }));

    setFormData(initialFormData);
    setSelectedOption(selectedOption);
  };

  const handleInputChange = (index, value) => {
    setFormData((prevFormData) => {
      const updatedFormData = [...prevFormData];
      updatedFormData[index].value = value;
      return updatedFormData;
    });
  };

  const handleRemoveField = (index) => {
    setFormData((prevFormData) => {
      const updatedFormData = [...prevFormData];
      updatedFormData.splice(index, 1);
      return updatedFormData;
    });
  };

  const handleAddField = () => {
    if (newKey && newValue) {
      setFormData((prevFormData) => [...prevFormData, { key: newKey, value: newValue }]);
      setNewKey("");
      setNewValue("");
    }
  };

  const handleSubmit = () => {
    // formData contains the key-value pairs for submission
    console.log(formData);

    // Add logic to save to the database or perform other actions
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
    <form>
      <div className="dfx">
        <div className="fieldWrap">
          <div className="Field">
            <label htmlFor="title">Title:</label>
            <input type="text" name="title" id="title" />
          </div>
        </div>

        <div className="fieldWrap">
          <div className="Field">
            <label htmlFor="status">Status:</label>
            <input type="checkbox" name="status" id="status" />
          </div>
        </div>
      </div>

      <div className="dfx">
        <div className="fieldWrap">
          <div className="Field">
            <Select options={options} onChange={handleSelectChange} />
          </div>
        </div>

        {selectedOption && (
          <div>
            {formData.map((field, index) => (
              <div key={field.key}>
                <label>{field.key}:</label>
                <input type="text" placeholder={field.key} value={field.value} onChange={(e) => handleInputChange(index, e.target.value)} />

                <SlClose className="delete-icon" onClick={() => handleRemoveField(index)} />
              </div>
            ))}
            <div>
              <input type="text" placeholder="Key" value={newKey} onChange={(e) => setNewKey(e.target.value)} />
              <input type="text" placeholder="Value" value={newValue} onChange={(e) => setNewValue(e.target.value)} />
              <button type="button" onClick={handleAddField}>
                Add
              </button>
            </div>
          </div>
        )}
      </div>

      <button type="button" onClick={handleSubmit}>
        Submit
      </button>
    </form>
  );
}

export default MyForm;
