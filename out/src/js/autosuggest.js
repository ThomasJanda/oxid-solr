
solrSuggest = {

    searchFieldId : 'searchParam',
    searchFieldResultsId : 'solrSearchResults',

    text_category:"", 
    text_manufacturer:"",
    text_category_main:"",

    source:null,
    searchField: null,
    searchFieldResults: null,
    searchResultController: null,
    searchResultTimer: null,

    init: function(source, text_category, text_manufacturer, text_category_main) {
        this.source = source;
        this.text_category = text_category;
        this.text_manufacturer = text_manufacturer;
        this.text_category_main = text_category_main;
        
        this.searchField = document.getElementById(this.searchFieldId);
        this.searchField.setAttribute("autocomplete","off");

        this.searchFieldResults = document.createElement("div");
        this.searchFieldResults.setAttribute('id',this.searchFieldResultsId);
        this.searchFieldResults.style.display = 'none';
        this.searchField.parentNode.insertBefore(this.searchFieldResults, this.searchField.nextSibling);

        var o = this;
        this.searchField.addEventListener('keyup', function(event) {
            var term = event.srcElement.value;
            if(o.searchResultController!==null) 
            {
                o.searchResultController.abort();
            }
            o.searchResultController = new AbortController();
            if(o.searchResultTimer!==null) 
            {
                clearTimeout(o.searchResultTimer);
                o.searchResultTimer=null;
            }
            if(term.length >=2 )
            {
                o.searchResultTimer = window.setTimeout(function(term) { o.executeDisplaySearchResult(term); }, 300, term);
            }
        });
        
        this.searchFieldResults.addEventListener('click', function(event) {
            event.stopPropagation();
        });
        document.getElementsByTagName('body')[0].addEventListener('click', function(event) {
            o.hideResult();
        });
    },

    executeDisplaySearchResult: function(term)
    {
        var o = this;
        var searchResultController = this.searchResultController;
        fetch(this.source + "?term=" + term, { searchResultController }).then(function(response) {
            /* receive the full response */
            if(response.ok)
                return response.json();
        }).then(function(json) {
            /* work with full data in the frontend */
            let lines = [];
            json.forEach(function(data) {
                let line = o.generateLine(data);
                if(line!=="")
                {
                    lines.push(line);
                }
            });

            let html = o.generateLines(lines);
            o.displayResult(html);
        });
    },

    generateLine: function(data)
    {
        line = "";
        if(data.type==="oxarticles")
        {
            let title = data.title;
            if(data.artnum!=="")
            {
                title += " (" + data.artnum + ")";
            }

            line = "<a href='" + data.link + "'>" +
                "  <div class='suggest-image'>" +
                "    <img src='" + data.image + "' title='" + data.title + "' />" +
                "  </div>" +
                "  <div class='suggest-title'>" +
                "    <span class='title'>" + title + "</span>" +
                "  </div>" +
                "</a>";
        }
        else
        {
            let title = this.text_category;
            if(data.type==="oxmanufactuer")
            {
                title = this.text_manufacturer;
            }
            else if(data.type==="oxcategory_main")
            {
                title = this.text_category_main
            }
            
            line = "<div class='suggest-group clearfix'><div>" + title + "</div>";
            data.items.forEach(function(data) {
                line += "<a href='" + data.link + "'>" +
                    "  <img src='" + data.image + "' title='" + data.title + "' />" +
                    "</a>";
            });
            line += "</div>";
        }

        return line;
    },

    generateLines: function(lines)
    {
        var html="<ul>";
        lines.forEach(function(line) {
            html+='<li><div>' + line + '</div></li>';
        });
        html += "</ul>";

        return html;
    },

    displayResult: function(html)
    {
        this.searchFieldResults.innerHTML = html;
        this.searchFieldResults.style.display = 'block';
    },
    
    hideResult: function()
    {
        var area = null;
        if(document.getElementById(this.searchFieldResultsId))
        {
            area = document.getElementById(this.searchFieldResultsId);
            area.style.display = 'none';
        }
        
    }
};

solrSuggest.init(source, text_category, text_manufacturer, text_category_main);
