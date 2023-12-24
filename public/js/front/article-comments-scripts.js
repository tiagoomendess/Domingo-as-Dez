let allow = true
const articleId = $('article').attr('data-id')
const user_id = parseInt($('#all_comments').attr('data-user-id'))

$(document).ready(function(){
    getComments(articleId, buildComments)
});

const processNewComment = () => {
    if (allow) {
        allow = false
        let commentSelector = $('#comment')
        let text = commentSelector.val()
        commentSelector.val(null)
        commentSelector.blur()

        if (text.length < 1)
            return

        $('#new_comment input').attr('disabled', true)

        sendComment(text, articleId, callbackNewComment)

        setTimeout(() => {
            allow = true
            $('#new_comment input').removeAttr('disabled')
            console.log("Can Comment again")
        }, 5000)
    }
}

const callbackNewComment = (response) => {

}

const sendComment = (comment, articleId, callback, commentId = null) => {
    let url = `/article_comments/${articleId}`
    if (commentId)
        url += `/${commentId}`

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            comment: comment
        },
        xhrFields: { withCredentials: true },
        crossDomain: true,
        dataType: 'json',
        success: function(response) { callback(response); },
        error: function() { console.error('Failed get request to ' + url) }
    });
}

const getComments = (articleId, callback) => {
    let url = `/article_comments/${articleId}`
    $.ajax({
        url: url,
        type: 'GET',
        crossDomain: true,
        dataType: 'JSON',
        success: function(response) { callback(response); },
        error: function() { console.error('Failed get request to ' + url) }
    });
}

const buildComments = (comments) => {

    let commentsDiv = $('#all_comments')

    comments.forEach(comment => {
        buildComment(comment, commentsDiv)
    })
}

const buildComment = (comment, appendTo, isReply = false) => {
    let newCommentDiv = $('#comment_template').clone()
    newCommentDiv.removeAttr('id')
    if (isReply)
        newCommentDiv.attr('style', 'margin-left: 40px')

    newCommentDiv.find('.comment-header > img').attr('src', comment.picture)
    newCommentDiv.find('.comment-header > span.text-bold').html(comment.name)
    newCommentDiv.find('.comment-header > small').html(comment.date)
    newCommentDiv.find('.comment-content > comment').html(comment.content)
    newCommentDiv.attr('data-user-id', comment.user_id)

    let form = newCommentDiv.find('form')
    form.attr('action', `/article_comments/${comment.id}/delete`)

    if (comment.user_id === user_id)
        form.removeClass('hide')

    newCommentDiv.removeClass('hide')

    appendTo.append(newCommentDiv)

    if (comment.replies) {
        comment.replies.forEach(reply => {
            buildComment(reply, newCommentDiv, true)
        })
    }
}
