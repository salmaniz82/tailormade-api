export default function AddSwatch() {
  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper bg-white">
          <h3 className="page-title"> Add New Swatch </h3>
        </div>

        <div className="wrapper bg-white mt-10">
          <ul>
            <li>select - stock collection</li>
            <li>input text - title</li>
            <li>price - input text</li>
            <li>load meta fields : this will map the meta field and prepare input field and label</li>
            <li>here user can just put values </li>
            <li>status shall be active by default</li>
            <li>Image Upload</li>
            <li>Thumnail generation</li>
            <li>when image is uploaded based on stock collection : prepare a table in database with, stock collection name alias</li>
            <li>/uploads/images/alias/imagefilename.extension</li>
            <li>/uploads/images/alias/thumnail.extension</li>
          </ul>
        </div>

        <div className="bg-white">
          <form action="">
            <div>
              <select name="stock-source" id="stock-source">
                <option value="">apple</option>
                <option value="">orange</option>
                <option value="">mango</option>
                <option value="">banana</option>
              </select>
            </div>
            <div>
              <input type="text" name="title" placeholder="add a title here" />
            </div>
          </form>
        </div>
      </main>
    </>
  );
}
