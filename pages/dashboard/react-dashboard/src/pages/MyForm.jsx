import React, { useState, useEffect } from "react";
import { API_BASE_URL } from "../utils/helpers";
import Select from "react-select";
import { SlClose, SlEye, SlList, SlNote, SlTrash, SlPlus } from "react-icons/sl";

function MyForm() {
  const [getMetaData, setMetaData] = useState([]);
  const [selectedOption, setSelectedOption] = useState(null);
  const [formData, setFormData] = useState([]);
  const [newKey, setNewKey] = useState("");
  const [newValue, setNewValue] = useState("");
  const [showAddFields, setShowAddFields] = useState(false);
  const [selectedImage, setSelectedImage] = useState(null);

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

  const handleImageChange = (event) => {
    const file = event.target.files[0];
    setSelectedImage(file);
  };

  const handleSubmit = () => {
    // formData contains the key-value pairs for submission
    console.log(formData);

    // selectedImage contains the selected file
    console.log(selectedImage);

    // Add logic to send data to the server, including the file
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
    <form name="add-swatch-form" id="add-swatch-form">
      <div className="dfx">
        <div className="dfx metaauto-fields">
          <label>Status:</label>
          <div>
            <label className="switch" htmlFor="status">
              <input type="checkbox" className="user-check-toggle" id="status" value="1" />
              <div className="slider round"></div>
            </label>
          </div>
          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="image">Image:</label>

          <div>
            <input type="file" id="image" accept="image/*" onChange={handleImageChange} />
          </div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="title">Title:</label>
          <input type="text" name="title" id="title" placeholder="Title" />
          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="stock-select">STOCK COLLECTION</label>
          <Select options={options} onChange={handleSelectChange} placeholder="Choose Stock Collection" id="stock-select" />
          <div>&nbsp;</div>
        </div>

        {selectedOption && (
          <div>
            {formData.map((field, index) => (
              <div key={field.key} className="dfx metaauto-fields">
                <label>{field.key}:</label>
                <input type="text" placeholder={field.key} value={field.value} onChange={(e) => handleInputChange(index, e.target.value)} />
                <SlClose className="delete-icon r-icons" onClick={() => handleRemoveField(index)} />
              </div>
            ))}

            <div className="dfx metaauto-fields">
              <input type="text" placeholder="Key" value={newKey} onChange={(e) => setNewKey(e.target.value)} />
              <input type="text" placeholder="Value" value={newValue} onChange={(e) => setNewValue(e.target.value)} />
              <SlPlus className="edit-icon r-icons" onClick={handleAddField} />
            </div>
          </div>
        )}
      </div>

      <div className="ImagePreview">
        {selectedImage && (
          <div>
            <img src={URL.createObjectURL(selectedImage)} alt="Selected" />
          </div>
        )}
      </div>

      <div className="flashButtonWrapper mx-auto">
        <div className="text_btn_lg" onClick={handleSubmit}>
          SAVE
        </div>
      </div>
    </form>
  );
}

export default MyForm;
