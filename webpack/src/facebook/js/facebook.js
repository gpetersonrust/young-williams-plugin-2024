 
function modifyOpenGraph() {
    console.log('open graph');
   // Check if Open Graph meta tags already exist
   const existingTitleTags = document.querySelectorAll('meta[property="og:title"]');
//    site_name
    const existingSiteNameTags = document.querySelectorAll('meta[property="og:site_name"]');
  
   const existingDescriptionTags = document.querySelectorAll('meta[property="og:description"]');
//    og:image
    const existingImageTags = document.querySelectorAll('meta[property="og:image"]');
    // og:image:url
    const existingImageUrlTags = document.querySelectorAll('meta[property="og:image:url"]');
    // image:secure_url
    const existingSecureImageTags = document.querySelectorAll('meta[property="og:image:secure_url"]');
   const existingUrlTags = document.querySelectorAll('meta[property="og:url"]');

    // loop through existing tags and remove them
    existingTitleTags.forEach(tag => tag.remove());
    existingDescriptionTags.forEach(tag => tag.remove());
    existingImageTags.forEach(tag => tag.remove());
    existingUrlTags.forEach(tag => tag.remove());
    existingSecureImageTags.forEach(tag => tag.remove());
    existingSiteNameTags.forEach(tag => tag.remove());
    existingImageUrlTags.forEach(tag => tag.remove());

    // create new tags
    metaTagCreator('property', 'title',  share_card_title);

    metaTagCreator('property', 'image:url', share_card_image );
    metaTagCreator('property', 'image:secure_url', share_card_image );
    metaTagCreator('property', 'image', share_card_image );
    metaTagCreator('property', 'description', share_card_description );
    metaTagCreator('property', 'url', share_card_url );
    metaTagCreator('property', 'site_name', share_card_site_title );

    // change page title to match share card title
    document.title = share_card_title;

 
 
}

function metaTagCreator(type, property, content){
    const metaTag = document.createElement('meta');
    metaTag.setAttribute(type, `og:${property}`);
    metaTag.setAttribute('content',  content);
    document.head.appendChild(metaTag);
}

modifyOpenGraph();