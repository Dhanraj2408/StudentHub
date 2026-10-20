
(function () {
    const dataType = document.getElementById("dataType");

    // Do not run on other Student Hub pages.
    if (!dataType) {
        return;
    }

    const searchInput = document.getElementById("searchInput");
    const categoryFilter = document.getElementById("categoryFilter");
    const sortOrder = document.getElementById("sortOrder");
    const loadingMessage = document.getElementById("loadingMessage");
    const errorMessage = document.getElementById("errorMessage");
    const resultCount = document.getElementById("resultCount");
    const dataContainer = document.getElementById("dataContainer");
    const previousPage = document.getElementById("previousPage");
    const nextPage = document.getElementById("nextPage");
    const pageInfo = document.getElementById("pageInfo");
    const offlineCache = document.getElementById("offlineCache");

    const files = {
        events: "events.json",
        students: "students.json",
        faqs: "faqs.json"
    };

    const pageSize = 6;

    let allData = [];
    let currentPage = 1;
    let requestNumber = 0;

    // Fetch JSON from the selected file.
    async function loadData() {
        const thisRequest = ++requestNumber;
        const type = dataType.value;

        loadingMessage.textContent = "Loading data...";
        loadingMessage.hidden = false;
        errorMessage.textContent = "";
        dataContainer.replaceChildren();
        resultCount.textContent = "";

        try {
            const response = await fetch(files[type]);

            if (!response.ok) {
                throw new Error(
                    "Could not load " + files[type] +
                    ". HTTP status: " + response.status
                );
            }

            const data = await response.json();

            if (!Array.isArray(data)) {
                throw new Error("The JSON file must contain an array.");
            }

            if (thisRequest !== requestNumber) return;

            allData = data;

            // Save the latest successful response for optional offline-like use.
            try {
                localStorage.setItem(
                    "studentHubCache_" + type,
                    JSON.stringify(data)
                );
            } catch (storageError) {
                console.warn("Data could not be cached:", storageError);
            }

            loadingMessage.hidden = true;
            currentPage = 1;
            updateFilters();
            renderData();

        } catch (error) {
            if (thisRequest !== requestNumber) return;

            // Use saved data only if the user enables the option.
            let cachedData = null;

            if (offlineCache.checked) {
                try {
                    const saved = localStorage.getItem(
                        "studentHubCache_" + type
                    );

                    if (saved) {
                        cachedData = JSON.parse(saved);
                    }
                } catch (storageError) {
                    console.warn("Saved data could not be read:", storageError);
                }
            }

            if (Array.isArray(cachedData)) {
                allData = cachedData;
                loadingMessage.hidden = true;
                errorMessage.textContent =
                    "Could not refresh the file. Showing saved data.";
                currentPage = 1;
                updateFilters();
                renderData();
            } else {
                allData = [];
                loadingMessage.hidden = true;
                errorMessage.textContent =
                    "Unable to load data. Run the site using a local server " +
                    "and check that " + files[type] + " exists.";
                updateFilters();
                renderData();
                console.error("Fetch error:", error);
            }
        }
    }

    // Return the text used for searching each record.
    function searchableText(item) {
        if (dataType.value === "events") {
            return item.title + " " + item.category + " " + item.location;
        }

        if (dataType.value === "students") {
            return item.name + " " + item.course + " " + item.email;
        }

        return item.question + " " + item.answer + " " + item.category;
    }

    // Populate the category/course dropdown.
    function updateFilters() {
        const oldValue = categoryFilter.value;
        categoryFilter.replaceChildren();

        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "All categories";
        categoryFilter.appendChild(defaultOption);

        const property =
            dataType.value === "students" ? "course" : "category";

        const values = [...new Set(
            allData.map(function (item) {
                return item[property];
            }).filter(Boolean)
        )].sort(function (a, b) {
            return a.localeCompare(b);
        });

        values.forEach(function (value) {
            const option = document.createElement("option");
            option.value = value;
            option.textContent = value;
            categoryFilter.appendChild(option);
        });

        if (values.includes(oldValue)) {
            categoryFilter.value = oldValue;
        }
    }

    // Search, filter and sort the data.
    function getProcessedData() {
        const query = searchInput.value.trim().toLowerCase();
        const selectedCategory = categoryFilter.value;

        let result = allData.filter(function (item) {
            const matchesSearch = searchableText(item)
                .toLowerCase()
                .includes(query);

            const property =
                dataType.value === "students" ? "course" : "category";

            const matchesCategory =
                !selectedCategory || item[property] === selectedCategory;

            return matchesSearch && matchesCategory;
        });

        if (sortOrder.value === "az" || sortOrder.value === "za") {
            const property = dataType.value === "events"
                ? "title"
                : dataType.value === "students"
                    ? "name"
                    : "question";

            result.sort(function (a, b) {
                const comparison = a[property].localeCompare(b[property]);

                return sortOrder.value === "az"
                    ? comparison
                    : -comparison;
            });
        }

        return result;
    }

    // Create cards safely using textContent.
    function createCard(item) {
        const card = document.createElement("article");
        card.className = "p6-card";

        const heading = document.createElement("h3");
        const details = document.createElement("p");
        const extra = document.createElement("p");
        const tag = document.createElement("span");

        if (dataType.value === "events") {
            heading.textContent = item.title;
            details.textContent = "Date: " + item.date;
            extra.textContent = "Location: " + item.location;
            tag.textContent = item.category;
        } else if (dataType.value === "students") {
            heading.textContent = item.name;
            details.textContent = "Course: " + item.course;
            extra.textContent = "Year: " + item.year +
                " | Email: " + item.email;
            tag.textContent = "Student";
        } else {
            heading.textContent = item.question;
            details.textContent = item.answer;
            extra.textContent = "";
            tag.textContent = item.category;
        }

        tag.className = "p6-tag";

        card.appendChild(heading);
        card.appendChild(details);

        if (extra.textContent) {
            card.appendChild(extra);
        }

        card.appendChild(tag);

        return card;
    }

    // Display the current page of results.
    function renderData() {
        const processedData = getProcessedData();
        const totalItems = processedData.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));

        currentPage = Math.min(currentPage, totalPages);
        currentPage = Math.max(1, currentPage);

        const start = (currentPage - 1) * pageSize;

        // slice() selects only the records for this page.
        const pageData = processedData.slice(start, start + pageSize);

        dataContainer.replaceChildren();

        if (pageData.length === 0) {
            const emptyMessage = document.createElement("p");
            emptyMessage.textContent = "No matching records found.";
            dataContainer.appendChild(emptyMessage);
        } else {
            // map() converts the selected records into cards.
            pageData.map(createCard).forEach(function (card) {
                dataContainer.appendChild(card);
            });
        }

        resultCount.textContent =
            "Total matching records: " + totalItems;

        pageInfo.textContent =
            "Page " + currentPage + " of " + totalPages;

        previousPage.disabled = currentPage === 1;
        nextPage.disabled = currentPage === totalPages || totalItems === 0;
    }

    // Event handlers
    dataType.addEventListener("change", function () {
        searchInput.value = "";
        sortOrder.value = "default";
        categoryFilter.value = "";
        currentPage = 1;
        loadData();
    });

    searchInput.addEventListener("input", function () {
        currentPage = 1;
        renderData();
    });

    categoryFilter.addEventListener("change", function () {
        currentPage = 1;
        renderData();
    });

    sortOrder.addEventListener("change", function () {
        currentPage = 1;
        renderData();
    });

    previousPage.addEventListener("click", function () {
        if (currentPage > 1) {
            currentPage--;
            renderData();
        }
    });

    nextPage.addEventListener("click", function () {
        const totalPages = Math.ceil(
            getProcessedData().length / pageSize
        );

        if (currentPage < totalPages) {
            currentPage++;
            renderData();
        }
    });

    // Initial data load
    loadData();
})();