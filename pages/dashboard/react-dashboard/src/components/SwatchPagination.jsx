export default function SwatchPagination({ pages, listMeta, handlePaginate }) {
  return (
    <>
      <div className="swatchPagination bg-white">
        <ul>
          {pages.length > 1 &&
            pages.map((pageNO, pageIndex) => (
              <li key={pageIndex} className={listMeta.page == pageNO ? "active-page" : ""}>
                <a href="#" onClick={(e) => handlePaginate(e, pageNO)}>
                  {pageNO}
                </a>
              </li>
            ))}
        </ul>
      </div>
    </>
  );
}
