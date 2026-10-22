

const CLOUD_NAME = "hagyoalb";
const UPLOAD_PRESET = "syria_news";

const GITHUB_REPO = "ahajqdwr96-arch/syria-news";
const NEWS_FILE = "news.json";


function status(message){

    document.getElementById("status").textContent = message;

}


function previewImage(){

    const file =
        document.getElementById("imageFile").files[0];

    const preview =
        document.getElementById("imagePreview");

    if(!file){

        preview.style.display = "none";

        return;
    }

    const reader = new FileReader();

    reader.onload = function(e){

        preview.src = e.target.result;

        preview.style.display = "block";

    };

    reader.readAsDataURL(file);
}


async function uploadCloudinary(file,type){

    if(!file){

        return "";
    }

    let resourceType = "image";

    if(type === "video" || type === "audio"){

        resourceType = "video";

    }

    const url =
        `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/${resourceType}/upload`;

    const formData = new FormData();

    formData.append("file",file);

    formData.append(
        "upload_preset",
        UPLOAD_PRESET
    );

    const response = await fetch(
        url,
        {
            method:"POST",
            body:formData
        }
    );

    if(!response.ok){

        const errorText =
            await response.text();

        throw new Error(
            "فشل رفع الملف:\n" +
            errorText
        );

    }

    const data =
        await response.json();

    return data.secure_url;
}


async function getNews(token){

    const url =
        `https://api.github.com/repos/${GITHUB_REPO}/contents/${NEWS_FILE}`;

    const response = await fetch(
        url,
        {
            headers:{
                "Authorization":
                    `Bearer ${token}`,

                "Accept":
                    "application/vnd.github+json",

                "X-GitHub-Api-Version":
                    "2022-11-28"
            }
        }
    );

    if(response.status === 404){

        return {
            news:[],
            sha:null
        };

    }

    if(!response.ok){

        const error =
            await response.text();

        throw new Error(
            "تعذر قراءة الأخبار من GitHub:\n" +
            error
        );

    }

    const data =
        await response.json();

    const binary =
        atob(
            data.content.replace(/\n/g,"")
        );

    const bytes =
        Uint8Array.from(
            binary,
            c => c.charCodeAt(0)
        );

    const text =
        new TextDecoder("utf-8")
        .decode(bytes);

    let news =
        JSON.parse(text);

    if(!Array.isArray(news)){

        news = [];

    }

    return {
        news:news,
        sha:data.sha
    };
}


async function saveNews(
    token,
    news,
    sha
){

    const url =
        `https://api.github.com/repos/${GITHUB_REPO}/contents/${NEWS_FILE}`;

    const json =
        JSON.stringify(
            news,
            null,
            2
        );

    const bytes =
        new TextEncoder()
        .encode(json);

    let binary = "";

    for(const byte of bytes){

        binary +=
            String.fromCharCode(byte);

    }

    const encoded =
        btoa(binary);

    const body = {

        message:
            "نشر خبر جديد",

        content:
            encoded,

        branch:
            "main"

    };

    if(sha){

        body.sha = sha;

    }

    const response =
        await fetch(
            url,
            {
                method:"PUT",

                headers:{

                    "Authorization":
                        `Bearer ${token}`,

                    "Accept":
                        "application/vnd.github+json",

                    "X-GitHub-Api-Version":
                        "2022-11-28",

                    "Content-Type":
                        "application/json"

                },

                body:
                    JSON.stringify(body)
            }
        );

    if(!response.ok){

        const error =
            await response.text();

        throw new Error(error);

    }

    return true;
}


async function publishNews(){

    const button =
        document.getElementById(
            "publishButton"
        );

    const token =
        document.getElementById(
            "token"
        ).value.trim();

    const title =
        document.getElementById(
            "title"
        ).value.trim();

    const category =
        document.getElementById(
            "category"
        ).value;

    const location =
        document.getElementById(
            "location"
        ).value.trim();

    const source =
        document.getElementById(
            "source"
        ).value.trim();

    const content =
        document.getElementById(
            "content"
        ).value.trim();

    const imageFile =
        document.getElementById(
            "imageFile"
        ).files[0];

    const videoFile =
        document.getElementById(
            "videoFile"
        ).files[0];

    const audioFile =
        document.getElementById(
            "audioFile"
        ).files[0];


    if(!token){

        status("❌ ضع GitHub Token أولاً.");

        return;
    }

    if(!title){

        status("❌ اكتب عنوان الخبر.");

        return;
    }

    if(!content){

        status("❌ اكتب نص الخبر.");

        return;
    }


    button.disabled = true;

    try{

        let image = "";
        let video = "";
        let audio = "";


        if(imageFile){

            status(
                "⏳ جاري رفع الصورة..."
            );

            image =
                await uploadCloudinary(
                    imageFile,
                    "image"
                );
        }


        if(videoFile){

            status(
                "⏳ جاري رفع الفيديو..."
            );

            video =
                await uploadCloudinary(
                    videoFile,
                    "video"
                );
        }


        if(audioFile){

            status(
                "⏳ جاري رفع المقطع الصوتي..."
            );

            audio =
                await uploadCloudinary(
                    audioFile,
                    "audio"
                );
        }


        status(
            "⏳ جاري قراءة الأخبار..."
        );

        const result =
            await getNews(token);


        const newsItem = {

            id:
                Date.now(),

            title:
                title,

            content:
                content,

            category:
                category,

            location:
                location,

            source:
                source,

            image:
                image,

            video:
                video,

            audio:
                audio,

            created_at:
                new Date().toISOString()

        };


        result.news.unshift(
            newsItem
        );


        status(
            "⏳ جاري نشر الخبر على الموقع..."
        );


        await saveNews(
            token,
            result.news,
            result.sha
        );


        status(
            "✅ تم نشر الخبر بنجاح."
        );


        clearForm(false);


    }catch(error){

        status(
            "❌ حدث خطأ:\n\n" +
            error.message
        );

    }finally{

        button.disabled = false;

    }

}


function clearForm(showMessage = true){

    document.getElementById(
        "title"
    ).value = "";

    document.getElementById(
        "location"
    ).value = "";

    document.getElementById(
        "source"
    ).value = "";

    document.getElementById(
        "content"
    ).value = "";

    document.getElementById(
        "imageFile"
    ).value = "";

    document.getElementById(
        "videoFile"
    ).value = "";

    document.getElementById(
        "audioFile"
    ).value = "";

    document.getElementById(
        "imagePreview"
    ).style.display = "none";


    if(showMessage){

        status(
            "تم مسح الحقول."
        );

    }

}




function loginAdmin(){

    const username =
        document.getElementById("loginUsername").value;

    const password =
        document.getElementById("loginPassword").value;

    if(
        username === "Muhammad" &&
        password === "muhammet4431"
    ){

        document.getElementById("loginBox").style.display =
            "none";

        sessionStorage.setItem(
            "admin_logged_in",
            "true"
        );

    }else{

        document.getElementById("loginError").style.display =
            "block";

    }
}

if(
    sessionStorage.getItem("admin_logged_in") === "true"
){

    document.getElementById("loginBox").style.display =
        "none";

}


    
async function loadNewsManager(){

    const token =
        document.getElementById("token").value.trim();

    const box =
        document.getElementById("newsManagerList");

    if(!token){
        box.innerHTML =
            "<p>❌ ضع GitHub Token أولاً.</p>";
        return;
    }

    box.innerHTML =
        "<p>⏳ جاري تحميل الأخبار...</p>";

    try{

        const result =
            await getNews(token);

        if(!result.news.length){

            box.innerHTML =
                "<p>لا توجد أخبار منشورة.</p>";

            return;
        }

        box.innerHTML = "";

        result.news.forEach(item => {

            const card =
                document.createElement("div");

            card.style.cssText =
                "background:#fff;border:1px solid #ddd;border-radius:10px;padding:15px;margin-top:12px;";

            card.innerHTML = `
                <h3>${item.title || "بدون عنوان"}</h3>

                <p>
                    ${item.content || ""}
                </p>

                <small>
                    ${item.location || ""}
                    ${item.category ? " — " + item.category : ""}
                </small>

                <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">

                    <button
                        type="button"
                        onclick="editNews('${item.id}')"
                    >
                        ✏️ تعديل
                    </button>

                    <button
                        type="button"
                        onclick="deleteNews('${item.id}')"
                        style="background:#b91c1c;color:white;"
                    >
                        🗑️ حذف
                    </button>

                </div>
            `;

            box.appendChild(card);

        });

    }catch(error){

        box.innerHTML =
            "<p>❌ تعذر تحميل الأخبار.</p><pre>" +
            error.message +
            "</pre>";

    }
}


async function deleteNews(id){

    if(!confirm("هل أنت متأكد من حذف هذا الخبر؟")){
        return;
    }

    const token =
        document.getElementById("token").value.trim();

    if(!token){
        status("❌ ضع GitHub Token أولاً.");
        return;
    }

    try{

        status("⏳ جاري حذف الخبر...");

        const result =
            await getNews(token);

        const news =
            result.news.filter(
                item => String(item.id) !== String(id)
            );

        await saveNews(
            token,
            news,
            result.sha
        );

        status("✅ تم حذف الخبر بنجاح.");

        loadNewsManager();

    }catch(error){

        status(
            "❌ فشل حذف الخبر:\n\n" +
            error.message
        );

    }
}


async function editNews(id){

    const token =
        document.getElementById("token").value.trim();

    if(!token){
        status("❌ ضع GitHub Token أولاً.");
        return;
    }

    try{

        const result =
            await getNews(token);

        const item =
            result.news.find(
                news => String(news.id) === String(id)
            );

        if(!item){
            status("❌ لم يتم العثور على الخبر.");
            return;
        }

        const newTitle =
            prompt(
                "عنوان الخبر:",
                item.title || ""
            );

        if(newTitle === null){
            return;
        }

        const newContent =
            prompt(
                "نص الخبر:",
                item.content || ""
            );

        if(newContent === null){
            return;
        }

        const newLocation =
            prompt(
                "الموقع:",
                item.location || ""
            );

        if(newLocation === null){
            return;
        }

        item.title =
            newTitle.trim();

        item.content =
            newContent.trim();

        item.location =
            newLocation.trim();

        await saveNews(
            token,
            result.news,
            result.sha
        );

        status("✅ تم تعديل الخبر بنجاح.");

        loadNewsManager();

    }catch(error){

        status(
            "❌ فشل تعديل الخبر:\n\n" +
            error.message
        );

    }
}


